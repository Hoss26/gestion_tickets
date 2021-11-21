<?php

namespace App\Controller;

use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\DateTime;


#[Route('/tickets')]


class TicketController extends AbstractController
{
    #[Route('/list_tous', name: 'tickets_list')]
    public function listtous(PaginatorInterface $paginator , Request $request): Response
    {
        $alltickets = $this->getDoctrine()->getRepository(Ticket::class)->findAll();
        $tickets  = $paginator->paginate($alltickets,  $request->query->getInt('page', 1),4);

        return $this->render('tickets/index.html.twig', ['tickets' => $tickets]);
    }
    /**
     *
     * @Route("/add/{titre}/{nompersonne}/{description}",name="add_ticket")
     */
    public function add($titre,$nom,$description)
    {
        $ticket = new Ticket();
        $ticket->setTitre($titre);
        $ticket->setNompersonne($nom);
        $ticket->setDescription($description);
        $ticket->setStatut("en attente");
        $ticket->setDatecreation(new \DateTime());

        $Manager = $this->getDoctrine()->getManager();
        $Manager->persist($ticket);
        $Manager->flush();

        return $this->redirectToRoute('tickets_list');
    }

    /**
     *
     * @Route("/update/{id}/{titre}/{nompersonne}/{description}", name="update_ticket")
     */
    public function update(Request $request,$titre,$nom,$description,$id )
    {

        $ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);
        if ($ticket){
            $ticket->setTitre($titre);
            $ticket->setNompersonne($nom);
            $ticket->setDescription($description);
            $ticket->setDate(new \DateTime());

            $Manager = $this->getDoctrine()->getManager();
            $Manager->persist($ticket);
            $Manager->flush();

        }

        return $this->redirectToRoute('tickets_list');}

    /**
     *
     * @Route("/delete/{id}", name="delete_ticket")
     */
    public function delete(Request $request, $id){
        $ticket = $this->getDoctrine()->getRepository(Ticket::class)->find($id);
        if (!$ticket) {
            return  $this->render('tickets/delete.html.twig', ['id' => $id]);
        }
        $Manager = $this->getDoctrine()->getManager();
        $Manager->remove($ticket);
        $Manager->flush();



        return $this->redirectToRoute('tickets_list');
    }
    #[Route('/list/{id}', name: 'tickets_list_id')]
    public function liste($id): Response
    {

        $tickets = $this->getDoctrine()->getRepository(Ticket::class)->findAll();


        return $this->render('tickets/id.html.twig', [
            'tickets' => $tickets,
            'id' => $id,]);
    }


    #[Route('/list_intervalle/{date_min}/{date_max}', name: 'tickets_list_intervalle')]
    public function list($date_min,$date_max): Response
    {

        $tickets = $this->getDoctrine()->getRepository(Ticket::class)->findAll();


        return $this->render('tickets/intervalle.html.twig', [
            'tickets' => $tickets,
            'dateMin' => $date_min,
            'dateMax' => $date_max,
        ]);
    }

}