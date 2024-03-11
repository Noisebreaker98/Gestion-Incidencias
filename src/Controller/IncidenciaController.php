<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Incidencia;
use App\Form\NuevaIncidenciaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class IncidenciaController extends AbstractController
{
    #[Route('/incidencias', name: 'verTodasIncidencias')]
    public function verTodasIncidencias(EntityManagerInterface $entityManager): Response
    {
        $incidencias = $entityManager->getRepository(Incidencia::class)->findBy([], ['fechaCreacion' => 'DESC']);
        
        return $this->render('incidencia/verTodasIncidencias.html.twig', [
            'incidencias' => $incidencias
        ]);
    }

    #[Route('/incidencia/{id}', name: 'verIncidencia')]
    #[IsGranted("ROLE_USER")]
    public function verIncidencia(Incidencia $incidencia): Response
    {
        return $this->render('incidencia/verIncidencia.html.twig', [
            'incidencia' => $incidencia,
        ]);
    }

    #[Route('/incidencias/add', name: 'addIncidencia')]
    #[IsGranted("ROLE_USER")]
    public function addIncidencia(EntityManagerInterface $entityManager, Request $request): Response
    {
        $incidencia = new Incidencia();
        $formularioIncidencia = $this->createForm(NuevaIncidenciaType::class, $incidencia);

        // Obtener el cliente predeterminado si se ha pasado como parámetro
        $clienteId = $request->query->get('clienteId');
        if ($clienteId) {
            $cliente = $entityManager->find(Cliente::class, $clienteId);
            if ($cliente) {
                $incidencia->setCliente($cliente);
                $formularioIncidencia->add('cliente', TextType::class, [
                    'disabled' => true,
                    'data' => $cliente->getNombre()
                ]);
            }
        }

        $incidencia->setUsuario($this->getUser());

        $formularioIncidencia->handleRequest($request);
        if ($formularioIncidencia->isSubmitted() && $formularioIncidencia->isValid()) {
            $entityManager->persist($incidencia);
            $entityManager->flush();

            // Mensaje de éxito
            $this->addFlash('success', '¡Incidencia creada con éxito!');

            return $this->redirectToRoute('verTodasIncidencias');
        }

        return $this->render('incidencia/addIncidencia.html.twig', [
            'formularioIncidencia' => $formularioIncidencia,
            'cliente' => $cliente ?? null
        ]);
    }


    #[Route('/incidencias/edit/{id}', name: 'editIncidencia')]
    #[IsGranted("ROLE_USER")]
    public function editarIncidencia(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $incidencia = $entityManager->find(Incidencia::class, $id);

        if (!$incidencia) {
            throw $this->createNotFoundException('Incidencia no encontrada');
        }

        // Creamos el formulario de nuevo cliente y lo asociamos al cliente existente
        $formularioIncidencia = $this->createForm(NuevaIncidenciaType::class, $incidencia);

        // Obtener el cliente de la incidencia actual
        $cliente = $incidencia->getCliente();

        // Si el cliente existe, establecer el campo de cliente como tipo texto y deshabilitado
        if ($cliente) {
            $formularioIncidencia->add('cliente', TextType::class, [
                'disabled' => true,
                'data' => $cliente->getNombre()
            ]);
        }

        $formularioIncidencia->handleRequest($request);

        if ($formularioIncidencia->isSubmitted() && $formularioIncidencia->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Incidencia actualizada correctamente');

            return $this->redirectToRoute('verTodasIncidencias');
        }

        return $this->render('incidencia/addIncidencia.html.twig', ['formularioIncidencia' => $formularioIncidencia]);
    }

    #[Route('/incidencias/delete/{id}', name: 'deleteIncidencia')]
    #[IsGranted("ROLE_USER")]
    public function deleteCategoria(Incidencia $incidencia, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($incidencia);
        $entityManager->flush();

        // Mensaje de éxito
        $this->addFlash('success', '¡Incidencia eliminada con éxito!');

        return $this->redirectToRoute('verTodasIncidencias');
    }
}
