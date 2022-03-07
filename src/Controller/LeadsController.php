<?php

namespace App\Controller;

use App\Entity\Leads;
use App\Entity\Landing;
use App\Form\LeadsImportType;
use function GuzzleHttp\json_decode;
use Spatie\SimpleExcel\SimpleExcelReader;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LeadsController extends AbstractController
{
    /**
     * @Route("/leads", name="leads")
     */
    public function index(Request $request): Response
    {

        $form = $this->createForm(LeadsImportType::class);
        $form->handleRequest($request);
        $newFilename = "";

        if ($form->isSubmitted() && $form->isValid()) {
            // for future version get the field of lp with api and display it 

            //search path of blob
            $data = $form->get('fichier')->getData();
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $newFilename = uniqid() . "." . $data->guessExtension();
            $data->move(
                $destination,
                $newFilename
            );
            $path = $destination . "/" . $newFilename;
            // $rows is an instance of Illuminate\Support\LazyCollection
            $rows = SimpleExcelReader::create($path)->getRows();

            $rows->each(function (array $rowProperties) {
                // in the first pass $rowProperties will contain
                // ['email' => 'john@example.com', 'first_name' => 'john']

            });
            return $this->render('leads/index.html.twig', [
                'form' => $form->createView(),
                'filename' => $newFilename
            ]);
        }
        return $this->render('leads/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/all_leads", name="all_leads")
     */
    public function allLeads(Request $request): Response
    {
        $landing = $this->getDoctrine()->getRepository(Landing::class)->findOneBy(["id" => 1]);
        $leads = $this->getDoctrine()->getRepository(Leads::class)->findBy(["landing_id"=>1, "status"=>"new"]);
        $headers = $landing->getdata();

        return $this->render('leads/all_Leads.html.twig', [
            'headers'=> $headers,
            'data'=> $leads
        ]);
    }
}
