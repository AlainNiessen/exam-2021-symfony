<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Livre;
use App\Form\LivreType;
use DateTime;

class LivreController extends AbstractController
{
    /**
     * @Route("/livre/form/creation", name="livreFormCreation")
     */
    public function formCreationLivre(Request $request, EntityManagerInterface $manager): Response
    {
        //création nouveau objet Livre
        $livre = new Livre();
        //setting de la date d'ajout et le nombre de votes avec des valeurs définiées en avance
        $livre -> setDateAjout(new DateTime());
        $livre -> setVotes(0);
        

        //création formulaire de création sur base de l'objet LivreType
        $formCreationLivre = $this -> createForm(LivreType::class, $livre);
        $formCreationLivre -> handleRequest($request);

        //si le fomulaire est submit et validé => récupération des données rentrées et insertion dans la base de données
        if($formCreationLivre -> isSubmitted() && $formCreationLivre -> isValid()) {
            //récupération de données rentrées dans le formulaire
            $livre = $formCreationLivre -> getData();

            //préparation insertion livre dans la base de données via entitymanager
            $manager -> persist($livre);
            //execution insertion
            $manager -> flush();

            //ajout d'un message de réussite d'insertion (affiché dans base.html.twig, alors elle sera affiché dans n'importe quel
            //cas de redirection de route)
            $this->addFlash('success', 'Livre créé avec succés');

            //redirect sur la liste des livres
            return $this->redirectToRoute('livreListe');
        }

        return $this->render('livre/formCreation.html.twig', [
            'formCreationLivre' => $formCreationLivre -> createView()
        ]);
    }

    /**
     * @Route("/livre/liste", name="livreListe")
     */
    public function listeLivres(EntityManagerInterface $manager): Response
    {
        //récupération du repository "Livre" 
        $repository = $manager -> getRepository(Livre::class);
        //récupération de tous les livres
        $livres = $repository -> findAll();
        

        return $this->render('livre/liste.html.twig', [
            'livres' => $livres
        ]);
    }

    /**
     * @Route("/livre/detail/{id}", name="livreDetail")
     */
    public function detailLivre(Livre $livre): Response
    {
        //ici récupération du livre via la requété automatique et son ID passé dans la route       

        return $this->render('livre/detail.html.twig', [
            'livre' => $livre
        ]);
    }

    /**
     * @Route("/livre/form/modif/{id}", name="livreFormModif")
     */
    public function formModifLivre(Livre $livre, Request $request, EntityManagerInterface $manager): Response
    {
        //pas de création d'un nouveau livre, mais récupération du livre via ID + remplissage des champs
        //de formulaire automatique (via réquete automatique)
        

        //création formulaire de création sur base de l'objet GenreType
        $formModifLivre = $this -> createForm(LivreType::class, $livre);
        $formModifLivre -> handleRequest($request);

        //si le fomulaire est submit et validé => récupération des données rentrées et insertion dans la base de données
        if($formModifLivre -> isSubmitted() && $formModifLivre -> isValid()) {
           
            //execution insertion (pas de persist nécessaire)
            $manager -> flush();

            //ajout d'un message de réussite de modification (affiché dans base.html.twig, alors elle sera affiché dans n'importe quel
            //cas de redirection de route)
            $this->addFlash('success', 'Livre modifié avec succés');

            //redirect sur la liste de livres
            return $this->redirectToRoute('livreListe');
        }

        return $this->render('livre/formModif.html.twig', [
            'formModifLivre' => $formModifLivre -> createView(),
            'livre' => $livre
        ]);
    }

    /**
     * @Route("/livre/supprimer/{id}", name="livreSupprim")
     */
    public function supprimerLivre(Livre $livre, EntityManagerInterface $manager): Response
    {
        //ici récupération du livre via la requété automatique et son ID passé dans la route 
        
        //préparation de la suppression du livre
        $manager -> remove($livre);
        //execution de la suppression
        $manager -> flush();

        //ajout d'un message de réussite de la suppression (affiché dans base.html.twig, alors elle sera affiché dans n'importe quel
        //cas de redirection de route)
        $this->addFlash('success', 'Livre supprimé avec succés');

        //redirect sur la liste de livres
        return $this->redirectToRoute('livreListe');
    }
}
