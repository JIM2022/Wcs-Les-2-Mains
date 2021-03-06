<?php
namespace CommerceBundle\Controller;

use CommerceBundle\Entity\Category;
use CommerceBundle\Entity\Product;
use CommerceBundle\Form\CategoryType;
use CommerceBundle\Form\ProductType;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ProductController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAllCategoriesAction(){
        $em = $this->getDoctrine()->getManager();

        $listcategories = $em->getRepository(\CommerceBundle\Entity\Category::class)->findAll();

        return $this->render('@Commerce/user/listAllCategories.html.twig', array(
            'listcategories' => $listcategories,
        ));
    }


    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addProductAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findAll();

        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(Category::class)->getCategory();

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product');
        }

        $category = new Category();
        $forms = $this->createForm(CategoryType::class, $category);
        $forms->handleRequest($request);

        if ($forms->isSubmitted() && $forms->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render('@Commerce/user/product.html.twig', array(
            'products' => $products,
            'categories' => $categories,
            'form' => $form->createView(),
            'forms' => $forms->createView(),
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editProductAction(Request $request)
    {
        if ($request->isXmlHttpRequest())
        {
            $em = $this->getDoctrine()->getManager();
            $idProduct = $request->request->get('idElem');
            $product = $em->getRepository(Product::class)->findOneById($idProduct);
            $form = $this->generateProductForm($product);
            $form->handleRequest($request);

            return $this->render('@Commerce/user/editProduct.html.twig', array(
                'product_selected' => $product,
                'form' => $form->createView()
            ));
        }
    }

    /**
     * @param Product $product
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function validEditProductAction(Product $product, Request $request)
    {
        $form = $this->generateProductForm($product);
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->redirectToRoute('product');
    }

    /**
     * @param $object
     * @return \Symfony\Component\Form\FormInterface
     */
    private function generateProductForm($object){
        $formBuilder = $this->get('form.factory')->createNamedBuilder('form_' . $object->getId(), ProductType::class, $object);
        $formBuilder->setAction($this->generateUrl('edit_valid_product', array(
            'id' => $object->getId()
        )));

        $form = $formBuilder->getForm();
        return $form;
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('CommerceBundle:Product')->findOneById($id);
        // Gestion des erreurs
        try{
            $em->remove($product);
            $em->flush();

            $this->addFlash(
                'notice',
                'Le produit est bien supprimé'
            );
        } catch(DBALException $e) {

            $this->addFlash(
                'notice',
                'Le produit est lié à un évenement et ne peut être supprimé'
            );
        }

        return $this->redirectToRoute('product');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function deleteCategoriesAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->request->get('idCat');

            $category = $em->getRepository('CommerceBundle:Category')->findOneById($id);


            $em->remove($category);
            $em->flush();

            return new Response('La Categorie ' . $category->getType() . ' ainsi que tous ses produits ont bien été supprimés');
        }

        return new Response('Une erreur est survenue, veuillez réessayer');
    }
}