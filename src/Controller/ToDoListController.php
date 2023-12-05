<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    #[Route('/', name: 'app_to_do_list')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tasks = $entityManager->getRepository(Task::class)->findBy([], ['id' => 'DESC']);
        return $this->render('index.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/create', name: 'create_task', methods: 'POST')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $title = trim($request->get('title'));
        if (empty($title)) {
            return $this->redirectToRoute('app_to_do_list');
        }

        $task = new Task;
        $task->setTitle($title);
        $entityManager->persist($task);
        $entityManager->flush();
        return $this->redirectToRoute('app_to_do_list');
    }

    #[Route('/switch-status/{id}', name: 'switch_status')]
    public function switchStatus(EntityManagerInterface $entityManager, Task $id): Response
    {
        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setStatus(!$task->isStatus());
        $entityManager->flush();
        return $this->redirectToRoute('app_to_do_list');
    }

    #[Route('/delete/{id}', name: 'task_delete')]
    public function delete(EntityManagerInterface $entityManager, Task $id): Response
    {
        $entityManager->remove($id);
        $entityManager->flush();
        return $this->redirectToRoute('app_to_do_list');
    }
}
