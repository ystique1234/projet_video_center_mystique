<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\SearchType;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Model\SearchData;

class VideoController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(Request $request,PaginatorInterface $paginator,VideoRepository $videoRepository,EntityManagerInterface $entityManager,TranslatorInterface $translator): Response {
              // Vérifier si l'utilisateur est connecté et vérifié
              $user = $this->getUser();
              if ($user && !$user->isVerified()) {
                  // Ajouter un message flash pour l'utilisateur non vérifié
                  $this->addFlash('info', $translator->trans('Your email address is not verified.'));
              }
      
              // Initialisation de la recherche
              $search = false;
              $searchData = new SearchData();
              $form = $this->createForm(SearchType::class, $searchData);
              $form->handleRequest($request);
      
              // Initialiser la variable pour les vidéos
              $videosQuery = $videoRepository->createQueryBuilder('v');
      
              if ($form->isSubmitted() && $form->isValid()) {
                  $searchData->page = $request->query->getInt('page', 1);
                  $videosQuery = $videoRepository->findBySearch($searchData);
                  $search = true;
              } else {
                  // Déterminer les vidéos à afficher en fonction du rôle de l'utilisateur
                  if ($user) {
                      if ($user->isVerified()) {
                          // Utilisateur connecté et vérifié : afficher toutes les vidéos, y compris les vidéos premium
                          $videosQuery = $videoRepository->createQueryBuilder('v')->getQuery();
                      } else {
                          // Utilisateur connecté mais non vérifié : afficher uniquement les vidéos non premium
                          $videosQuery = $videoRepository->createQueryBuilder('v')
                              ->where('v.isPremium = false')
                              ->getQuery();
                      }
                  } else {
                      // Utilisateur non connecté : afficher uniquement les vidéos non premium
                      $videosQuery = $videoRepository->createQueryBuilder('v')
                          ->where('v.isPremium = false')
                          ->getQuery();
                  }
              }
      
              // Pagination des vidéos
              $videos = $paginator->paginate(
                  $videosQuery, /* query NOT result */
                  $request->query->getInt('page', 1), /* page number */
                  9 /* limit per page */
              );
      
              // Rendu du template avec les vidéos
              return $this->render('video/index.html.twig', [
                  'form' => $form->createView(),
                  'videos' => $videos,
                  'search' => $search,
                  'searchData' => $searchData->query,
              ]);
          }
      

    #[Route('/video/create', name: 'app_video_create')]
    public function create(Request $request, EntityManagerInterface $entityManager,TranslatorInterface $translator ): Response
    {
        if ($this->getUser()){
            if(!$this->getUser()->isVerified()){
                $this->addFlash('error',$translator->trans('You must confirm your email to create Vidéo!'));
                return $this->redirectToRoute('app_home');
            }
        }else{
            $this->addFlash('error', $translator->trans('You must login to create Video !'));
            return $this->redirectToRoute('app_login');
        }
        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setUser($this->getUser());
            $video->setCreatedAt(new DateTimeImmutable());
            $video->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash('success','*La vidéo'.$video->getTitle() . 'a bien été crééé');

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/create.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/video/{id}', name: 'app_video_show', methods: ['GET'])]
    public function show(int $id, Video $video , VideoRepository $videoRepository,TranslatorInterface $translator): Response
    {

        // Récupérer la vidéo par son ID
        $video = $videoRepository->find($id);

        // Vérifier si la vidéo existe
        if (!$video) {
            throw $this->createNotFoundException('La vidéo n\'existe pas.');
        }

         // Vérifier si l'utilisateur est connecté
         if (!$this->getUser()) {
            $this->addFlash('error',$translator->trans('You must be logged in to view the video.'));
            return $this->redirectToRoute('app_login');
        }

          // Vérifier si l'utilisateur est vérifié (ex: email confirmé, etc.)
          if (!$this->getUser()->isVerified()) {
            // return $this->render('video/index.html.twig', [
            //     'message' => 'Please verify your account to access premium content.',
            // ]);
            $this->addFlash('error',$translator->trans('Please verify your account to access premium content.'));
            return $this->redirectToRoute('app_home');
        }

        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/video/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager,TranslatorInterface $translator): Response
    {

        if ($this->getUser()){
            if (!$this->getUser()->isVerified()){
                $this->addFlash('error', $translator->trans('You must confirm your email to edit Video !'));
                return $this->redirectToRoute('app_Video');    
            }else if ($video->getUser()->getEmail() !== $this->getUser()->getEmail()) {
                $this->addFlash('error',$translator->trans('You must to be the user'.$video->getUser()->getEmail().'to edit this Video !'));
                return $this->redirectToRoute('app_home');
            }
        }else{
            $this->addFlash('error', $translator->trans('You must login to edit Video !'));
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();
            $this->addFlash('success', $translator->trans('The video has been modified'));

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_video_delete', methods: ['POST'])]
    public function delete(Request $request, Video $video, EntityManagerInterface $entityManager,TranslatorInterface $translator): Response
    {
        if ($this->getUser()){
            if (!$this->getUser()->isVerified()){
                $this->addFlash('error', 'You must confirm your email to delete Recipe !');
                return $this->redirectToRoute('app_recipe_index');
            }else if ($video->getUser()->getEmail() !== $this->getUser()->getEmail()) {
                $this->addFlash('error',$translator->trans('You must to be the user'.$video->getUser()->getEmail().'to delete this Recipe !'));
                return $this->redirectToRoute('app_recipe_index');
            }
        }else{
            $this->addFlash('error', $translator->trans('You must login to delete Vidéo !'));
            return $this->redirectToRoute('app_login');
        }
       
            $titre = $video->getTitle();
            $entityManager->remove($video);
            $entityManager->flush();
            $this->addFlash('info', $translator->trans('The Video' .$titre. 'a bien été supprimée'));
        

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}

