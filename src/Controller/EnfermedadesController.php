<?php

namespace App\Controller;

use App\Entity\Enfermedades;
use App\Form\EnfermedadesType;
use App\Repository\EnfermedadesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


/**
 * @Route("/")
 */
class EnfermedadesController extends AbstractController
{
    /**
     * @Route("/", name="app_enfermedades_index", methods={"GET", "POST"})
     */
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {
        $form =$this->createFormBuilder()
        ->add('file',FileType::class,[
            'label'=> 'Archivo Excel.(xlsx)',
            'mapped' => false,

               
            'required' => true,
        ])
        ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
        $file= ($form['file']->getData()); // get the file from the sent request
   
        $fileFolder = __DIR__ . '/../../exels/';  //choose the folder in which the uploaded file will be stored
  
        $filePathName= md5(uniqid()) .'.'. $file->getClientOriginalName();
      // apply md5 function to generate an unique identifier for the file and concat it with the file extension  
            try {
                $file->move($fileFolder, $filePathName);
            } catch (FileException $e) {
                throw  new \Exception('Error al subir archivo');
            }
         $spreadsheet = IOFactory::load($fileFolder . $filePathName); // Here we are able to read from the excel file 
         $row = $spreadsheet->getActiveSheet()->removeRow(1); // I added this to be able to remove the first file line 
         $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true); // here, the read data is turned into an array
         //dd($sheetData);

        
         foreach ($sheetData as $Row) 
             { 
                
                 $nombre = $Row['A'];
                 $codigo = $Row['B']; 
                 $descripcion= $Row['C']; 
                 $c_descripcion =$Row['D'];
                  
      
                     $m = new Enfermedades(); 
                     $m->setNombre($nombre);
                     $m->setCodigo($codigo);
                     $m->setDescripcion($descripcion);
                     $m->setCodigoDescripcion($c_descripcion);
                     $entityManager->persist($m);
                     $entityManager->flush();
                     
                 
             } 
                return $this->redirect($request->getUri()); 
            }

      

            $data['recordsTotal'] = $entityManager
                ->createQuery('SELECT count(sd) FROM App:Enfermedades sd')
                ->getSingleScalarResult();
           


        return $this->render('enfermedades/index.html.twig', [
            'data' => $data,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/server-processing", name="server_processing")
     */
    public function serverProcessing(EntityManagerInterface $entityManager)
    {
      

        /*ORDER BY sd.id DESC*/

        $dql = 'SELECT sd FROM App:Enfermedades sd';
        $dqlCountFiltered = 'SELECT count(sd) FROM App:Enfermedades sd';

        $sqlFilter = '';

        if (!empty($_GET['search']['value'])) {
            $strMainSearch = $_GET['search']['value'];

            $sqlFilter .= "
                sd.nombre LIKE '%".$strMainSearch."%' OR "
                ."sd.descripcion LIKE '%".$strMainSearch."%' OR "
                ."sd.codigo LIKE '%".$strMainSearch."%' OR "
                ."sd.codigo_descripcion LIKE '%".$strMainSearch."%'
                ";
        }

        // Filter columns with AND restriction
        $strColSearch = '';
        foreach ($_GET['columns'] as $column) {
            if (!empty($column['search']['value'])) {
                if (!empty($strColSearch)) {
                    $strColSearch .= ' AND ';
                }
                $strColSearch .= ' sd.'.$column['name']." LIKE '%".$column['search']['value']."%'";
            }
        }
        if (!empty($sqlFilter)) {
            $sqlFilter .= ' AND ('.$strColSearch.')';
        } else {
            $sqlFilter .= $strColSearch;
        }

        if (!empty($sqlFilter)) {
            $dql .= ' WHERE'.$sqlFilter;
            $dqlCountFiltered .= ' WHERE'.$sqlFilter;
            /*var_dump($dql);
            var_dump($dqlCountFiltered);
            exit;*/
        }

        //var_dump($dql); exit;

        $items = $entityManager
            ->createQuery($dql)
            ->setFirstResult($_GET['start'])
            ->setMaxResults($_GET['length'])
            ->getResult();

        $data = [];
        foreach ($items as  $value) {
            $data[] = [
               
                $value->getNombre(),
                $value->getCodigo(),
                $value->getDescripcion(),
                $value->getCodigoDescripcion(),
                $value->getId()
            ];
        }

        $recordsTotal = $entityManager
            ->createQuery('SELECT count(sd) FROM App:Enfermedades sd')
            ->getSingleScalarResult();

        $recordsFiltered = $entityManager
            ->createQuery($dqlCountFiltered)
            ->getSingleScalarResult();

        return $this->json([
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
            'dql' => $dql,
            'dqlCountFiltered' => $dqlCountFiltered,
        ]);
    }

     /**
     * @Route("/add_task", name="add_task")
     */
    public function add(): Response
    {
        $form2= $this->createForm(EnfermedadesType::class);
        return $this->render('enfermedades/add_task.html.twig', [
            'form2'=> $form2->createView()
        ]);
    }


    /**
     * @Route("/new", name="app_enfermedades_new")
     */
    public function new(Request $request, EntityManagerInterface $entityManager)
    {
        $nombre= $request->get('nombre');
        $codigo= $request->get('codigo');
        $descripcion= $request->get('descripcion');
        $codigo_descripcion= $request->get('codigo_descripcion');
        
            $task= new Enfermedades();
            $task->setNombre($nombre);
            $task->setCodigo($codigo);
            $task->setDescripcion($descripcion);
            $task->setCodigoDescripcion($codigo_descripcion);
            $entityManager->persist($task);
            $flush= $entityManager->flush();
            if($flush == null){
                $msg="Tarea agregada";
            }else{
                $msg="Errore en el envio de datos";
            }
        return new Response($msg);
    }



    /**
     * @Route("/{id}/show", name="app_enfermedades_show", methods={"GET"})
     */
    public function show(Enfermedades $enfermedade): Response
    {
        return $this->render('enfermedades/show.html.twig', [
            'enfermedade' => $enfermedade,
        ]);
    }

    /**
     * @Route("/adit_task", name="edit_task")
     */
    public function editar(): Response
    {
        $form2= $this->createForm(EnfermedadesType::class);
        return $this->render('enfermedades/edit_task.html.twig', [
            'form2'=> $form2->createView()
        ]);
    }

    /**
     * @Route("/edit", name="app_enfermedades_edit")
     */
    public function edit(Request $request,EntityManagerInterface $entityManager)
    {
        $data= $request->get('postData');
        $nombre= $data['nombre'];
        $codigo= $data['codigo'];
        $descripcion= $data['descripcion'];
        $codigo_descripcion= $data['codigo_descripcion'];
        $id= $data['id'];

        $task= $entityManager->getRepository(Enfermedades::class)->find($id);

        $task->setNombre($nombre);
        $task->setCodigo($codigo);
        $task->setDescripcion($descripcion);
        $task->setCodigoDescripcion($codigo_descripcion);
        $entityManager->persist($task);
        $flush= $entityManager->flush();
        if($flush == null){
            $msg="Tarea editada";
        }else{
            $msg="Errore en el envio de datos";
        }

    
    return new Response($msg);
       
    }

      /**
     * @Route("/delete", name="delete")
     */
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id= $request->get('id');
        $task= $entityManager->getRepository(Enfermedades::class)->find($id);
        $entityManager->remove($task);
        $flush= $entityManager->flush();
        
        if($flush == null){
            $msg="Tarea eliminada";
        }else{
            $msg="Errore en el envio de datos";
        }

    
    return new Response($msg);
        
    }


}
