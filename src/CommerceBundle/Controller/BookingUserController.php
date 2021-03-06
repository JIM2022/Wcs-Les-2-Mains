<?php

namespace CommerceBundle\Controller;


use CommerceBundle\Entity\Evenement;
use CommerceBundle\Entity\Marchandise;
use CommerceBundle\Entity\Product;
use CommerceBundle\Entity\SelectProduit;
use CommerceBundle\Entity\User;
use CommerceBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class BookingUserController extends Controller
{

    /**
     * Show all evenements (ventes)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listEvenenementAction()
    {
        // La liste des Evenements Crées en BDD
        $em = $this->getDoctrine()->getManager();

	    $evenements = $em->getRepository(Evenement::class)->getEvenementMoreThanNow();

	    if ($this->getUser())
        {
	        $lastEvenements = $em->getRepository(Evenement::class)->getEvenementLessThanNow();
	        return $this->render('@Commerce/user/listAllEvenements.html.twig', array(
		        'evenements'=> $evenements,
		        'lastEvenements' => $lastEvenements
	        ));
        }

        // On retourne à la vue tous les évenements
        return $this->render('@Commerce/user/listAllEvenements.html.twig', array(
            'evenements'=> $evenements
        ));
    }

    public function checkIfUserExistForOneEventAction(Request $request)
    {
    	if ($request->isXmlHttpRequest()){
		    // Check if user exist
		    $prenom = strtolower($request->request->get('prenom'));
		    $nom = strtolower($request->request->get('nom'));
		    $id = $request->request->get('idEvent');

		    $em = $this->getDoctrine()->getManager();
		    $event = $em->getRepository(Evenement::class)->findOneById($id);
		    $existingUser = $em->getRepository(User::class)->findOneBy(array(
			    'prenom' => $prenom,
			    'nom' => $nom,
			    'evenement' => $event
	        ));

		    $existing = $existingUser != null ? 1 : 0;

		    $panier = array(
		    	'nom' => $nom,
			    'prenom' => $prenom
		    );

		    $session = $request->getSession();
		    $session->set('panier', $panier);

		    return new Response($existing);

	    }

	    throw new NotFoundHttpException('Sorry not existing!');
    }

    /**
     * Create basket
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bookingAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = new User();

        // On récupère toutes les marchandises misent à disposition par l'évènement et on les stock dans une variable $marchandises
        $evenement = $em->getRepository(Evenement::class)->getEvenementById($id);
        $marchandises = $em->getRepository(Marchandise::class)->getMarchandiseById($evenement['id']);

//        $e = $em->getRepository(Evenement::class)->findOneById($id);
//        $marchandises = $e->getMarchandises();
        $categories = array();
        $i = 0;
        // Pour chaque marchandise à l'intérieur du tableau de marchandise (le tableau de marchandise correspond à toutes les marchandises disponible dans l'evenement)
        // La boucle permet de générer autant de formulaire qu'il y a de produit disponible lors de l'évènement afin de pouvoir les reserver (collectionType)
        foreach ($marchandises as $marchandise){
        	$categorieName = $marchandise->getProduct()->getCategories()->getType();
	        $categorieNameWithoutSpecialChar = str_replace(str_split(" 'áàâäãåçéèêëíìîïñóòôöõúùûüýÿ"), '_', strtolower($categorieName));
            $categories[$categorieNameWithoutSpecialChar][$i] = $marchandise;

            // Création d'un new objet de la table SelectProduit
            $selectProduit = new SelectProduit();
            // Associer un produit à un produit selectionné
            $selectProduit->setProduct($marchandise->getProduct());
            // ajout de ts les produits crées dans la Var User
            $user->addSelectProduit($selectProduit);
            $i ++ ;

        }

	    // Création d'un formulaire User(Panier)
        $formUser = $this->createForm(UserType::class, $user);
        $formUser->handleRequest($request);

        if ($formUser->isSubmitted() && $formUser->isValid())
        {
	        // Je récupère la session utilisateur
	        $session = $request->getSession();
	        $panier = $session->get('panier');

	        $panier['selectProduit'] = array();

            // Pour chaque produit selectionner par le user, je récupère les informations (prix, quantités, idProduit) et les stock dans mon panier
            foreach ($user->getSelectProduits() as $key => $selectProduit)
            {
                // Je récupère la quantité de produit selectionné
                $quantity = $selectProduit->getQuantiteCommande();

                // Si le produit à été selectionné (la quantitée est supérieur à 0), je calcul le prix total lié au produit
                if ($quantity > 0){

                    // Prix unitaire du produit selectionné
                    $prixUnitaire = $marchandises[$key]->getPrix();

                    $prixTotal = $quantity * $prixUnitaire;

                    $panier['selectProduit'][$key] = array(
                        'prixTotal' => $prixTotal,
                        'quantiteCommande' => $quantity,
                        'idProduit' => $selectProduit->getProduct()->getId(),
                        'name' => $selectProduit->getProduct()->getName()
                    );
                }
            }

            // Je récupère la session utilisateur
            $session = $request->getSession();

            // Ma session se comporte comme un tableau associatif, donc à l'index panier, je stock ma variable $panier qui représente le contenu de mon panier (produits selectionnés par l'utilisateur)
            $session->set('panier', $panier);


            // Je retourne à la vue 'Recap' mon panier ($user) afin que le user puisse valider son panier
            return $this->render('@Commerce/user/recap.html.twig', array(
                'panier' => $panier,
                'evenement' => $evenement,
            ));
        }

        // Sinon retour à la vue de notre formulaire
        return $this->render('@Commerce/user/booking.html.twig', array(
            'marchandise' => $marchandises,
            'evenement' => $evenement,
            'categories'=> $categories,
            'formUser' => $formUser->createView(),
	        'user' => $user
        ));

    }
	/**
	 * @param Evenement $evenement
	 * @return Response
	 */
	public function RecapBookingAction(Evenement $evenement)
	{
		$em = $this->getDoctrine()->getManager();
		$recaps = $em->getRepository(Evenement::class)->getEvenement($evenement->getId());

		return $this-> render('@Commerce/user/recapBooking.html.twig', array(
			'recaps' => $recaps,
			'evenement' => $evenement
		));
	}

    /**
     * @param Request $request
     * @param Evenement $evenement
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validBookingAction(Request $request, Evenement $evenement)
    {
        $em = $this->getDoctrine()->getManager();
	    $session = $request->getSession();
	    $panier = $session->get('panier');

	    $user = new User();

	    $user->setNom($panier['nom']);
	    $user->setPrenom($panier['prenom']);
	    $user->setEvenement($evenement);

	    foreach ($panier['selectProduit'] as $value) {
		    $produit = $em->getRepository(Product::class)->findOneById($value['idProduit']);

		    // Décrémentation de la quantité initial en fonction de la quantité commandé
            $marchandise = $em->getRepository(Marchandise::class)->getCheckMarchandiseById($evenement, $produit->getId());
            $currentQuantite = $marchandise->getQuantite();
            $requestQuantite = $value['quantiteCommande'];
            $marchandise->setQuantite($currentQuantite - $requestQuantite);

		    $selectProduit = new SelectProduit();
		    $selectProduit->setPrixTotal($value['prixTotal']);
		    $selectProduit->setQuantiteCommande($value['quantiteCommande']);
		    $selectProduit->setProduct($produit);
		    $selectProduit->setUser($user);
		    $user->addSelectProduit($selectProduit);
	    }
	    $em = $this->getDoctrine()->getManager();
	    $em->persist($user);
	    $session->remove('panier');

	    $em->flush();

	    return $this->redirectToRoute('recap_booking', array(
		    'id' => $evenement->getId()
	    ));
    }
}

