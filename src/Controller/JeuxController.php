<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Jeux;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\JeuxType;


class JeuxController extends AbstractController
{
    #[Route('/jeux', name: 'app_jeux')]
    public function index(EntityManagerInterface $em, Request $r, SluggerInterface $slugger): Response
    {
        // $jeux = new Jeux();
        // $form = $this->createForm(Jeux::class, $jeux);
        // $form->handleRequest($r);

        $jeuxs = $em->getRepository(Jeux::class)->findAll();
        return $this->render('jeux/index.html.twig', [
            'jeuxs' => $jeuxs,
        ]);
    }
    #[Route('/jeux/add', name: 'app_add_jeux')]
    public function create(EntityManagerInterface $em, Request $r, SluggerInterface $slugger): Response
    {
        $game = new Jeux();
        $form = $this->createForm(JeuxType::class, $game);
        $form->handleRequest($r);
        $roleUser = $this->getUser()->getRoles();

        if ($form->isSubmitted() && $form->isValid() && in_array('ROLE_ADMIN', $roleUser)) {
            $slug = $slugger->slug($game->getName() . '-' . uniqid());
            $game->setSlug($slug);
            // prepare the query
            $em->persist($game);
            // execute the query
            $em->flush();
        }

        $jeuxs = $em->getRepository(Jeux::class)->findAll();

        return $this->render('jeux/addJeux.html.twig', [
            'jeuxs' => $jeuxs,
            'form' => $form->createView()
        ]);
    }
    #[Route('/jeux/update/{slug}', name: 'app_update_jeux')]
    public function update(EntityManagerInterface $em, Request $r, SluggerInterface $slugger, $slug): Response
    {
        $game = $em->getRepository(Jeux::class)->findOneBy(['slug' => $slug]);

        $roleUser = $this->getUser()->getRoles();
        if (!$game) {
            throw $this->createNotFoundException('No game found for slug ' . $slug);
        }
        if (!in_array('ROLE_ADMIN', $roleUser)) {

            $form = $this->createForm(JeuxType::class, $game);
            $form->handleRequest($r);
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($game->getName() . '-' . uniqid());
            $game->setSlug($slug);
            // execute the query
            $em->flush();
        }

        $jeuxs = $em->getRepository(Jeux::class)->findAll();

        // dd($jeuxs);
        // dd($form->createView());
        return $this->render('jeux/UpdateJeux.html.twig', [
            'jeuxs' => $jeuxs,
            'form' => $form->createView()
        ]);
    }
    #[Route('/jeux/delete/{id}', name: 'app_delete_jeux')]
    public function delete(EntityManagerInterface $em, $id, Request $r): Response
    {
        $game = $em->getRepository(Jeux::class)->find($id);
        $roleUser = $this->getUser()->getRoles();   

        if (!$game) {
            throw $this->createNotFoundException('No game found for id ' . $id);
        }
        if ($this->isCsrfTokenValid('delete' . $game->getId(), $r->request->get('csrf')) && in_array('ROLE_ADMIN', $roleUser)) {

            $em->remove($game);
            $em->flush();
        }

        return $this->redirectToRoute('app_jeux');
    }

}
