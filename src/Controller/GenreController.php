<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Genre;
use App\Form\GenreType;

class GenreController extends AbstractController
{
    /**
     * @Route("/genre/form/creation", name="genreFormCreation")
     */
    public function formCreationGenre(Request $request, EntityManagerInterface $manager): Response
    {
        //création nouveau objet Genre
        $genre = new Genre();

        //création formulaire de création sur base de l'objet GenreType
        $formCreationGenre = $this -> createForm(GenreType::class, $genre);
        $formCreationGenre -> handleRequest($request);

        //si le fomulaire est submit et validé => récupération des données rentrées et insertion dans la base de données
        if($formCreationGenre -> isSubmitted() && $formCreationGenre -> isValid()) {
            //récupération de données rentrées dans le formulaire
            $genre = $formCreationGenre -> getData();

            //préparation insertion genre dans la base de données via entitymanager
            $manager -> persist($genre);
            //execution insertion
            $manager -> flush();

            //ajout d'un message de réussite d'insertion (affiché dans base.html.twig, alors elle sera affiché dans n'importe quel
            //cas de redirection de route)
            $this->addFlash('success', 'Genre créé avec succés');

            //redirect sur la liste des genres
            return $this->redirectToRoute('genreListe');
        }

        return $this->render('genre/formCreation.html.twig', [
            'formCreationGenre' => $formCreationGenre -> createView()
        ]);
    }
    /**
     * @Route("/genre/liste", name="genreListe")
     */
    public function listeGenres(EntityManagerInterface $manager): Response
    {
        //récupération du repository "Genre" 
        $repository = $manager -> getRepository(Genre::class);
        //récupération de tous les genres 
        $genres = $repository -> findAll();
        

        return $this->render('genre/liste.html.twig', [
            'genres' => $genres
        ]);
    }

    /**
     * @Route("/genre/form/modif/{id}", name="genreFormModif")
     */
    public function formModifGenre(Genre $genre, Request $request, EntityManagerInterface $manager): Response
    {
        //pas de création d'un nouveau genre, mais récupération du genre via ID + remplissage des champs
        //de formulaire automatique (via réquete automatique)
        

        //création formulaire de modification sur base de l'objet GenreType
        $formModifGenre = $this -> createForm(GenreType::class, $genre);
        $formModifGenre -> handleRequest($request);

        //si le fomulaire est submit et validé => récupération des données rentrées et insertion dans la base de données
        if($formModifGenre -> isSubmitted() && $formModifGenre -> isValid()) {
           
            //execution insertion (pas de persist nécessaire)
            $manager -> flush();

            //ajout d'un message de réussite de modification (affiché dans base.html.twig, alors elle sera affiché dans n'importe quel
            //cas de redirection de route)
            $this->addFlash('success', 'Genre modifié avec succés');

            //redirect sur la liste des genres
            return $this->redirectToRoute('genreListe');
        }

        return $this->render('genre/formModif.html.twig', [
            'formModifGenre' => $formModifGenre -> createView(),
            'genre' => $genre
        ]);
    }
}
