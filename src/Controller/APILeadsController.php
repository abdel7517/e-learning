<?php

namespace App\Controller;

use App\Entity\Leads;
use LDAP\Result;
use Spatie\SimpleExcel\SimpleExcelReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Service\Contact\Mail;

class APILeadsController extends AbstractController
{
    private $header;

    private $mailer;
    public function  __construct(Mail $mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * @Route("/api/headers", name="api_leads_header")
     */
    public function index(Request $request)
    {
        $headers = [];
        $filename = $request->getContent();
        // find all files in the current directory
        $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;
        $headers = SimpleExcelReader::create($path)->getHeaders();
        foreach ($headers as $header) {
            $headers[] =  $header;
        }
        return new JsonResponse(json_encode($headers, JSON_FORCE_OBJECT), 200, [], true);
    }

    /**
     * @Route("/api/add", name="api_leads_add")
     */
    public function add(Request $request)
    {
        $body = $request->getContent();
        $headers =  json_decode($body);
        $this->header = $headers[0];
        $filename = $headers[1];
        $path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;
        $rows = SimpleExcelReader::create($path)->getRows();
        $test = $rows->each(function (array $rowProperties) {
            $lead = new Leads;
            $data = [];
            // in the first pass $rowProperties will contain
            // ['email' => 'john@example.com', 'first_name' => 'john']
            foreach ($this->header as $keyLp => $keySheet) {
                $data[$keyLp] =  $rowProperties[$keySheet];
                // foreach ($rowProperties as $rowKey => $rowValue) {
                //     if ($keySheet = $rowKey)
                //         $data[$keyLp] = $rowValue;
                // }
            }
            $data["commentaire"] = "";
            $lead->setData($data);
            $lead->setLandingId(1);
            $lead->setStatus("new");
            $em = $this->getDoctrine()->getManager();
            $em->persist($lead);
            $em->flush();
        });

        return new Response("ok");
    }
    /**
     * @Route("/api/addField", name="api_leads_addField")
     */
    public  function addField(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $leads = $this->getDoctrine()->getRepository(Leads::class)->findAll();
        $em = $this->getDoctrine()->getManager();
        foreach ($leads as $lead) {
            $data = $lead->getData();
            $data[$payload["key"]] = $payload["value"];
            $lead->setData($data);
            $em->persist($lead);
        }
        $em->flush();
        $data = $lead->getData();
        return new Response("fait");
    }

    /**
     * @Route("/api/register/{newState}/{id}", name="api_leads_changeState")
     */
    public function changeState(Request $request, String $newState, String $id)
    {
        $lead = $this->getDoctrine()->getRepository(Leads::class)->findOneBy(["id" => $id]);
        $lead->setStatus($newState);
        $em = $this->getDoctrine()->getManager();
        $em->persist($lead);
        $em->flush();
        return new Response('ok');
    }

    /**
     * @Route("/api/mailInfo", name="api_leads_infoMail")
     */
    public function infoMail(Request $request)
    {
        $idLead = $request->getContent();
        $lead = $this->getDoctrine()->getRepository(Leads::class)->findOneBy(["id" => $idLead]);
        $mail = str_replace(' ', '', $lead->getData()["Mail"]);
        $this->mailer->sendInfo($mail,  "https://www.moncompteformation.gouv.fr/espace-prive/html/#/connexion");
        return new Response("ok");
    }

    /**
     * @Route("/api/updateField", name="api_leads_updateField")
     */
    public function updateField(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $lead = $this->getDoctrine()->getRepository(Leads::class)->findOneBy(["id" => $payload["id"]]);
        $data = $lead->getData();
        
        if($payload["key"] !== "commentaire" ){
            $data[$payload["key"]] = str_replace(' ', '', $payload["value"]);
        }else{
            $data[$payload["key"]] = $payload["value"];
        }
        $lead->setData($data);
        $em = $this->getDoctrine()->getManager();
        $em->persist($lead);
        $em->flush();
        return new Response();
    }

    /**
     * @Route("/api/linkFormationMail", name="api_leads_linkFormationMail")
     */
    public function linkFormationMail(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $lead = $this->getDoctrine()->getRepository(Leads::class)->findOneBy(["id" => $data["mail"]]);
        $mail = str_replace(' ', '', $lead->getData()["Mail"]);
        $lien = $data["link"];
        $this->mailer->sendInfo($mail,  $lien);
        return new Response("ok");
    }

    /**
     * @Route("/api/register/{id}/{start}/{end}", name="api_leads_registerDate")
     */
    public function registerDate($id, $start, $end)
    {
        $lead = $this->getDoctrine()->getRepository(Leads::class)->findOneBy(["id" => $id]);
        $data = $lead->getData();
        $data["start"] = $start;
        $data["end"] = $end;
        $lead->setData($data);
        $em = $this->getDoctrine()->getManager();
        $em->persist($lead);
        $em->flush();
        return new Response("ok");
    }
    /**
     * @Route("/api/get/leads/{type}", name="api_leads_get")
     */
    public function getLeads($type)
    {
        $data = [];
        $leads = $this->getDoctrine()->getRepository(Leads::class)->findBy(["landing_id" => 1, "status" => $type]);
        foreach ($leads as $lead) {
            $newdata =  $lead->getData();
            $newdata["id"] = $lead->getId();
            $data[] = json_encode($newdata);
        }
        return new Response(json_encode($data, JSON_FORCE_OBJECT));
    }

    /**
     * @Route("/api/get/leads/with/{fieldName}", name="api_leads_getWith")
     */
    public function getLeadsWith(Request $request, $fieldName)
    {
        $data = json_decode($request->getContent());
        $leads = $this->getDoctrine()->getRepository(Leads::class)->findByField($fieldName, $data);
        return new Response(json_encode($leads));
    }
}
