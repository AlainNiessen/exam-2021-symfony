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
        $livre -> setDateAjout(new DateTime());
        $livre -> setVotes(0);
        

        //création formulaire de création sur base de l'objet GenreType
        $formCreationLivre = $this -> createForm(LivreType::class, $livre);
        $formCreationLivre -> handleRequest($request);

        //si le fomulaire est submit et validé => récupération des données rentrées et insertion dans la base de données
        if($formCreationLivre -> isSubmitted() && $formCreationLivre -> isValid()) {
            //récupération de données rentrées dans le formulaire
            $livre = $formCreationLivre -> getData();

            //préparation insertion genre dans la base de données via entitymanager:::::
            $manager -> persist($livre);
            //execution insertion
            $manager -> flush();

            //ajout d'un message de réussite d'insertion (affiché dans base.html.twig, alors elle sera affiché dans n'importe quel
            //cas de redirection de route)
            $this->addFlash('success', 'Livre créé avec succés');

            //redirect
            return $this->redirectToRoute('home');
        }

        return $this->render('livre/formCreation.html.twig', [
            'formCreationLivre' => $formCreationLivre -> createView()
        ]);
    }
}
