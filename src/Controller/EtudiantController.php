<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class EtudiantController extends AbstractController
{
    #[Route('/etudiant/insert', name: 'app_etudiant', methods: ['POST'])]
    public function insert(EntityManagerInterface $entity_manager,Request $request): Response
    {
        $data=json_decode($request->getContent(),true);
        // if(!$data){
        //     return $this->json([
        //         'status' => 'error',
        //         'message' => 'Invalid data'
        //     ], Response::HTTP_BAD_REQUEST);
        // }
        try {
            $etudiant = new Etudiant();
            $etudiant->setNom($data['nom']);
            $etudiant->setMoyenne($data['moyenne']);
            $entity_manager->persist($etudiant);
            $entity_manager->flush();
            return $this->json([
                'status' => 'success',
                'message' => 'Data inserted successfully'
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to insert data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/etudiant/getAll', name: 'app_etudiant_get', methods: ['GET'])]
    public function get(EtudiantRepository $etudiantRepository): Response
    {
        try {
            $etudiants = $etudiantRepository->findAll();
            return $this->json( $etudiants,200, [], ['groups' => ['etudiant:read']]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/etudiant/get/{id}', name: 'app_etudiant_get_id', methods: ['GET'])]
    public function getById(EtudiantRepository $etudiantRepository, int $id): Response
    {
        try {
            $etudiant = $etudiantRepository->find($id);
            if (!$etudiant) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Etudiant not found'
                ], Response::HTTP_NOT_FOUND,[], ['groups' => ['etudiant:read']]);
            }
            return $this->json([
                'status' => 'success',
                'data' => $etudiant
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/etudiant/update/{id}', name: 'app_etudiant_update',methods: ['PATCH'])]
    public function update(EntityManagerInterface $entity_manager, EtudiantRepository $etudiantRepository, int $id, Request $request): Response
    {
        $data=json_decode($request->getContent(),true);
        if(!$data){
            return $this->json([
                'status' => 'error',
                'message' => 'Invalid data'
            ], Response::HTTP_BAD_REQUEST);
        }
        try {
            $etudiant = $etudiantRepository->find($id);
            if (!$etudiant) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Etudiant not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $etudiant->setNom($data['nom']);
            $etudiant->setMoyenne($data['moyenne']);
            $entity_manager->flush();
            return $this->json([
                'status' => 'success',
                'message' => 'Data updated successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to update data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/etudiant/delete/{id}', name: 'app_etudiant_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entity_manager, EtudiantRepository $etudiantRepository, int $id): Response
    {
        try {
            $etudiant = $etudiantRepository->find($id);
            if (!$etudiant) {
                return $this->json([
                    'status' => 'error',
                    'message' => 'Etudiant not found'
                ], Response::HTTP_NOT_FOUND);
            }
            $entity_manager->remove($etudiant);
            $entity_manager->flush();
            return $this->json([
                'status' => 'success',
                'message' => 'Data deleted successfully'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => 'Failed to delete data'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
