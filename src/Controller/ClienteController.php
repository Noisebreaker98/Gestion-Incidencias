<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Form\NuevoClienteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ClienteController extends AbstractController
{
    #[Route('/clientes', name: 'verTodosClientes')]
    #[IsGranted("ROLE_USER")]
    public function verTodosClientes(EntityManagerInterface $entityManager): Response
    {
        $clientes = $entityManager->getRepository(Cliente::class)->findAll();

        return $this->render('cliente/verTodosClientes.html.twig', [
            'clientes' => $clientes
        ]);
    }

    #[Route('/cliente/{id}', name: 'verCliente')]
    #[IsGranted("ROLE_USER")]
    public function verCliente(Cliente $cliente): Response
    {
        return $this->render('cliente/verCliente.html.twig', [
            'cliente' => $cliente,
        ]);
    }

    #[Route('/clientes/add', name: 'addCliente')]
    #[IsGranted("ROLE_USER")]
    public function addCliente(EntityManagerInterface $entityManager, Request $request): Response
    {
        $cliente = new Cliente();

        $formularioCliente = $this->createForm(NuevoClienteType::class, $cliente);

        $formularioCliente->handleRequest($request);
        if ($formularioCliente->isSubmitted() && $formularioCliente->isValid()) {

            $cliente = $formularioCliente->getData();

            $entityManager->persist($cliente);
            $entityManager->flush();

            // Mensaje de éxito
            $this->addFlash('success', '¡Cliente creado con éxito!');

            return $this->redirectToRoute('verTodosClientes');
        }

        return $this->render('cliente/addCliente.html.twig', ['formularioCliente' => $formularioCliente]);
    }

    #[Route('/clientes/edit/{id}', name: 'editCliente')]
    #[IsGranted("ROLE_USER")]
    public function editarCliente(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $cliente = $entityManager->find(Cliente::class,$id);

        if (!$cliente) {
            throw $this->createNotFoundException('Cliente no encontrado');
        }

        // Creamos el formulario de nuevo cliente y lo asociamos al cliente existente
        $formularioCliente = $this->createForm(NuevoClienteType::class, $cliente);

        $formularioCliente->handleRequest($request);

        if ($formularioCliente->isSubmitted() && $formularioCliente->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Cliente actualizado correctamente');

            return $this->redirectToRoute('verTodosClientes');
        }

        return $this->render('cliente/addCliente.html.twig', ['formularioCliente' => $formularioCliente]);
    }

    #[Route('/clientes/delete/{id}', name: 'deleteCliente')]
    #[IsGranted("ROLE_USER")]
    public function deleteCategoria(Cliente $cliente, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($cliente);
        $entityManager->flush();

         // Mensaje de éxito
        $this->addFlash('success', '¡Cliente eliminado con éxito!');

        return $this->redirectToRoute('verTodosClientes');
    }
}
