<?php

namespace App\Http\Controllers\Admin;

use App\Models\Catalogue;
use App\Models\CatalogueType;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CatalogueExport;
use Laracasts\Flash\Flash;
use App\Traits\HelperTrait;
use Illuminate\Http\Request;
use App\Models\MasonCategory;
use App\Http\Controllers\Controller;
use App\DataTables\CatalogueDataTable;
use App\Http\Requests\Catalogue\CreateCatalogueRequest;
use App\Http\Requests\Catalogue\UpdateCatalogueRequest;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use ZipArchive;

class CatalogueController extends Controller
{
    use HelperTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CatalogueDataTable $dataTable, Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.view') ;
        return $dataTable->render('admin.catalogue.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.create') ;
        $masonCategory = MasonCategory::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $masonCategory = ['' => 'Select Mason Category'] + $masonCategory;

        $catalogueTypes = CatalogueType::where('status', CatalogueType::ACTIVE_STATUS)->pluck('name', 'id')->toArray();
        $catalogueTypes = ['' => 'Select Catalogue Type'] + $catalogueTypes;
        
        return view('admin.catalogue.create')->with('categoryOption', $masonCategory)
            ->with('catalogueTypes', $catalogueTypes)
            ->with('categorySelected', "")
            ->with('catalogueTypeSelected', "")
            ->with('statusSelected',"");
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCatalogueRequest $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.create') ;
        try {
            $input = $request->except(['image']);
            $masonCategory = $this->getMasonCategoryByPoint($input["point"]);
            if(empty($masonCategory))
            {
                throw new \Exception("No mason category found based on the point.");
            }
            $input["mason_category_id"] = $masonCategory->id;
            $catalogue = Catalogue::create($input) ;
            $code = 'CAT'.str_pad($catalogue->id, 4, "0", STR_PAD_LEFT);

            $catalogue->update(['catalogue_code' => $code]) ;

            if($request->has('image')){
                $data = $this->uploadFile($request->file('image'), 'catalogues') ;
                $catalogue->update(['image' => $data['path']]) ;
            }

            //------------------ Log Entry ----------------------
            $logData = [
                'table_id' => $catalogue->id,
                'user_id' => \Auth::user()?->id,
                'model_name' => 'Catalogue',
                'request'=> json_encode($request->all()) ,
                'response'=> json_encode($catalogue->refresh()) ,
                'action' => 'create',
            ];

            $this->createLog($logData) ;

            Flash::success('Catalogue saved successfully.');
            return redirect(route('catalogues.index'));
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('catalogues.index'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.view') ;
        return $id;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.edit') ;
        $catalogue = Catalogue::find($id);

        if (empty($catalogue)) {
            Flash::error('catalogue not found');
            return redirect(route('catalogues.index'));
        }

        $masonCategory = MasonCategory::orderBy('name', 'DESC')->pluck('name', 'id')->toArray();
        $masonCategory = ['' => 'Select Mason Category'] + $masonCategory;

        $catalogueTypes = CatalogueType::where('status', CatalogueType::ACTIVE_STATUS)->pluck('name', 'id')->toArray();
        $catalogueTypes = ['' => 'Select Catalogue Type'] + $catalogueTypes;

        return view('admin.catalogue.edit')->with('catalogue', $catalogue)
                ->with('categoryOption', $masonCategory)
                ->with('categorySelected', $catalogue->mason_category_id)
                ->with('catalogueTypes', $catalogueTypes)
                ->with('catalogueTypeSelected', $catalogue->catalogue_type_id)
                ->with('statusSelected', $catalogue->status);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCatalogueRequest $request, $id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.edit') ;
        try {
            $catalogue = catalogue::find($id);
            $oldValues = $catalogue->getOriginal();
            
            if (empty($catalogue)) {
                Flash::error('Catalogue not found');
                return redirect(route('catalogues.index'));
            }
        
            $input = $request->except(['image']) ;
            $masonCategory = $this->getMasonCategoryByPoint($input["point"]);
            if(empty($masonCategory))
            {
                throw new \Exception("No mason category found based on the point.");
            }
            $input["mason_category_id"] = $masonCategory->id;
            $result =  $catalogue->update($input);
            if(!empty($request->image)){
                if(file_exists(public_path($catalogue->image))){
                    unlink(public_path($catalogue->image));
                }
                $data = $this->uploadFile($request->file('image'), 'catalogues') ;
                $catalogue->update(['image' => $data['path']]) ;
            }




            //---------------- Log Entry -------------------
            $changedValues = $catalogue->getChanges();
            $diff = [] ;

            foreach ($changedValues as $key => $item) {
               $diff[$key] = [
                'old_data' =>  $oldValues[$key],
                'new_data' =>  $item,
               ];
            }

            $logData = [
                'table_id' => $catalogue->id,
                'model_name' => 'Catalogue',
                'user_id' => \Auth::user()->id,
                'request' => json_encode($request->all()),
                'response' => json_encode($changedValues),
                'action' => 'Update',
                'data_updated' => json_encode($diff),
            ];

            $this->createLog($logData) ;

            Flash::success('Catalogue updated successfully.');

            return redirect(route('catalogues.index'));
        } 
        catch (\Exception $e) {
            Flash::Error('Error: '. $e->getMessage());
            return redirect(route('catalogues.index'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.delete') ;
        $catalogue = Catalogue::find($id);
        if (empty($catalogue)) {
            Flash::error('catalogue not found');
            return redirect(route('catalogues.index'));
        }
        if(!empty($catalogue->image) && file_exists(public_path($catalogue->image))){
            unlink(public_path($catalogue->image));
        }
        $catalogue->delete();

        Flash::success('catalogue deleted successfully.');
        return redirect(route('catalogues.index'));
    }
    public function showBulkUploadForm(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.bulk-upload') ;
        // $request->session()->forget('catalogue_import');
        if($request->session()->exists('dealer_import')){
            return view('admin.catalogue.progress') ;
        }
       
        return view('admin.catalogue.bulk-upload') ;
      
    }
    public function uploadCsvFile(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.bulk-upload') ;
        set_time_limit(0);
        try {
            // return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Service is temporary unavailable.'], 200);
            if($request->hasFile('csvFile'))
            {
                $file       = $request->file('csvFile');
                $folderPath = \Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  \Storage::disk('public')->path($folderPath) ;
                $records=[];
                if (($handle = fopen($fileWithPath, 'r')) !== FALSE) { // Check the resource is valid
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Check opening the file is OK!
                        $records[]= $data; // Array
                    }
                    fclose($handle);
                }
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = 0; $unProcessedCount = 0;
                $unprocessedData=[];
              //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
                session()->put('catalogue_import', $fileWithPath) ;
                session()->put('catalogue_count', 0) ;
                foreach ($records as $key => $row) 
                {
                    if($key > 0){
                        if(!isset($row[0]) || $row[0] === null || $row[0] === '')
                        {
                            array_push($unprocessedData,"<br>Name of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        elseif(!isset($row[1]) || $row[1] === null || $row[1] === '')
                        {
                            array_push($unprocessedData,"<br>Description of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        elseif(!isset($row[2]) || $row[2] === null || $row[2] === '')
                        {
                            array_push($unprocessedData,"<br>Point of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        elseif(empty($masonCategory = $this->getMasonCategoryByPoint($row[2])))
                        {
                            array_push($unprocessedData,"<br>No mason category found based on the point in row ".($key + 1)." ");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        elseif(!isset($row[3]) || $row[3] === null || $row[3] === '')
                        {
                            array_push($unprocessedData,"<br>Status of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        elseif(!Catalogue::checkValidStatusCode($row[3]))
                        {
                            array_push($unprocessedData,"<br>Status value of row ".($key + 1)." is invalid");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        elseif(!isset($row[4]) || $row[4] === null || $row[4] === '')
                        {
                            array_push($unprocessedData,"<br>Catalogue type of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        elseif(!CatalogueType::find($row[4]))
                        {
                            array_push($unprocessedData,"<br>Catalogue type value of row ".($key + 1)." is invalid");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        elseif(!isset($row[5]) || $row[5] === null || $row[5] === '')
                        {
                            array_push($unprocessedData,"<br>Image Name of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        else
                        {
                            $imageName = trim($row[5]);
                            $imageSourcePath = public_path('catalogues/'.$imageName);

                            if (str_contains($imageName, '..') || str_contains($imageName, '/') || str_contains($imageName, '\\')) {
                                array_push($unprocessedData,"<br>Image Name of row ".($key + 1)." is invalid");
                                $unProcessedCount++;
                                continue;
                            }

                            if (!file_exists($imageSourcePath)) {
                                array_push($unprocessedData,"<br>Image Name of row ".($key + 1)." was not found in catalogues folder");
                                $unProcessedCount++;
                                continue;
                            }

                            $mimeType = File::mimeType($imageSourcePath);
                            try {
                                if (strpos(strtolower($mimeType ?? ''), 'image/') !== 0) {
                                    throw new \Exception("Not a valid image file");
                                }
                            } catch (\Throwable $ex) {
                                array_push($unprocessedData, "<br>Image Name of row ".($key + 1)." is not a valid image.");
                                $unProcessedCount++; 
                                continue;
                            }
                        }

                        //After validating 
                        $catalogue = Catalogue::where('name', $row[0])->where("point", $row[2])->first() ;
                        $catalogueTypeID = $row[4];
                                
                        if(empty($catalogue))
                        {
                            $tempCatalogue =  Catalogue::create([
                                'name' => mb_convert_encoding($row[0] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                                'mason_category_id' => mb_convert_encoding($masonCategory->id, 'UTF-8', 'ISO-8859-1'),
                                'description' => mb_convert_encoding($row[1] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                                'point' => mb_convert_encoding($row[2] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                                'status' => mb_convert_encoding($row[3] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                                'image' => mb_convert_encoding('catalogues/'.trim($row[5]), 'UTF-8', 'ISO-8859-1'),
                                'catalogue_type_id' => mb_convert_encoding($catalogueTypeID, 'UTF-8', 'ISO-8859-1'),
                            ]);
                            $updatedCatalogueCode = 'CAT'.str_pad($tempCatalogue->id, 4, "0", STR_PAD_LEFT);

                            $tempCatalogue->update(['catalogue_code' => $updatedCatalogueCode]) ;
                        }
                        else
                        {
                            // Catalogue Update is Commented Out.

                            // $oldImagePath = $catalogue->image;
                            
                            // $uploadImage = $this->uploadDownloadedFileFromLink($imageFromLink, 'catalogues') ;
                            // $catalogue->update([
                            //     'name' => mb_convert_encoding($row[0] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                            //     'mason_category_id' => mb_convert_encoding($masonCategory->id, 'UTF-8', 'ISO-8859-1'),
                            //     'description' => mb_convert_encoding($row[1] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                            //     'point'       => mb_convert_encoding($row[2] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                            //     'status'      => mb_convert_encoding($row[3] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                            //     'image' => mb_convert_encoding($uploadImage['path'] ?? NULL, 'UTF-8', 'ISO-8859-1'),
                            //     'catalogue_type_id' => mb_convert_encoding($catalogueTypeID, 'UTF-8', 'ISO-8859-1'),
                            // ]) ;
                            // if(file_exists(public_path($oldImagePath))){
                            //     unlink(public_path($oldImagePath));
                            // }
                        }

                        $count++ ;
                        session()->put('catalogue_count', $count) ;
                            
                        // $request->session()->save();
                        // sleep(10) ;
        
                        if($count == $lineSllice)
                        {
                            $request->session()->save();
                            sleep(1) ;
                        }
                                
                        //  echo session()->get('catalogue_count');
                
                    }
                }
                $request->session()->forget('catalogue_import');

                if(session()->get('catalogue_count') > 0)
                {
                    Catalogue::where('created_at', '<', Carbon::today())
                    ->where('status', 1)
                    ->update([
                        'status' => Catalogue::STATUS_DISABLE
                    ]);
                }

                $responseMessage = 'Import Successfull, '.session()->get('catalogue_count').' records processed';
                if($unProcessedCount > 0)
                {
                    $responseMessage .= ' & <span class="text-danger">'.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData).'</span>';
                }
                else
                {
                    $responseMessage .= '.';
                }
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> $responseMessage], 200);
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('catalogue_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200);
    
           
        }
         
    }

    // Progress of Bulk Upload
    public function getProgress(Request $request)
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.bulk-upload') ;
        if($request->session()->has('catalogue_import'))
        {
            return response()->json(['success'=> true,
             'import_status'=> 0, 'records'=> session()->get('catalogue_count'), 'message'=> 'Importing Data. Please wait....'], 200);

        }

        return response()->json(['success'=> true, 'import_status'=> 1,
         'message'=> 'Import Successfull '.session()->get('catalogue_count').' records processed.'], 200);

    }
    // Bulk Image Upload

    public function showImagesUploadForm()
    {
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.bulk-upload') ;
        //Flash::Warning('Service is unavailable.');
       // return redirect(route('catalogues.index'));
        return view('admin.catalogue.images-upload') ;
    }
    public function uploadImages(Request $request)
{
    \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.bulk-upload');

    try {
        set_time_limit(0);

        if (!$request->hasFile('zipFile')) {
            return response()->json([
                'success' => false,
                'import_status' => 1,
                'message' => 'Zip file is required.'
            ], 200);
        }

        $request->validate([
            'zipFile' => 'required|file|mimes:zip',
        ]);

        $zipFile = $request->file('zipFile');

        // Save ZIP temporarily
        $zipPath = storage_path('app/temp_' . time() . '.zip');
        $zipFile->move(dirname($zipPath), basename($zipPath));

        $extractPath = public_path('catalogues');

        if (!File::exists($extractPath)) {
            File::makeDirectory($extractPath, 0755, true);
        }

        // IMPORTANT: escape shell arguments
        $command = "unzip -o " . escapeshellarg($zipPath) . " -d " . escapeshellarg($extractPath);

        exec($command, $output, $returnCode);
       /* exec($command . ' 2>&1', $output, $returnCode);

        dd([
            'command' => $command,
            'output' => $output,
            'returnCode' => $returnCode
        ]);*/

        // Delete temp zip
        @unlink($zipPath);

        if ($returnCode !== 0) {
            return response()->json([
                'success' => false,
                'import_status' => 1,
                'message' => 'Unzip failed. Please check server configuration.'
            ], 200);
        }

        return response()->json([
            'success' => true,
            'import_status' => 1,
            'message' => 'Zip extracted successfully using Linux unzip command.'
        ], 200);

    } catch (\Throwable $ex) {
        return response()->json([
            'success' => false,
            'import_status' => 1,
            'message' => 'Error: ' . $ex->getMessage()
        ], 200);
    }
}
    public function cataloguesExport() 
    {
        return Excel::download(new CatalogueExport, 'Catalogues.xlsx');
    }

    public function downloadCatalogueFormat(){
        set_time_limit(0);

        $filename = "Catalogue_format_".$this->getUniqueId().".csv";

        $headings = [
            "Cataogue Code",
            "Name",
            "Point",
            "Status",
        ];

        $myfile = fopen(public_path("/format/").$filename, "w");

        fprintf($myfile, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($myfile,$headings);


         foreach(Catalogue::cursor() as $val)
        {
           
            $content = [
               
                $val->catalogue_code,
                $val->name,
                $val->point,
                $val->status,
            ];
            fputcsv($myfile,$content);
        }

        fclose($myfile);
        $filePath = public_path("/format/".$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function bulkUpdateStatusShow(Request $request)  {
     
        return view('admin.catalogue.bulk-status-edit');
    }

    public function updateBulkStatus(Request $request)  {

    
        \Helper::checkIsUserAuthorizeToPerformTheTask('catalogues.bulk-upload') ;
        set_time_limit(0);

        try {
            // return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Service is temporary unavailable.'], 200);
            if($request->hasFile('csvFile'))
            {
                $file       = $request->file('csvFile');
                $folderPath = \Storage::disk('public')->put('temp', $file);
                $actualPath = storage_path($folderPath);
                $fileWithPath =  \Storage::disk('public')->path($folderPath) ;
                $records=[];
                if (($handle = fopen($fileWithPath, 'r')) !== FALSE) { // Check the resource is valid
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) { // Check opening the file is OK!
                        $records[]= $data; // Array
                    }
                    fclose($handle);
                }
                $lineSllice = floor(count($records) / 100) > 0 ? floor(count($records) / 100) : 1;
                $count = 0; $unProcessedCount = 0;
                $unprocessedData=[];
              //  $process = Process::create(['file_path' =>  $fileWithPath , 'total_line'=> count($records)-1 , 'line_processed'=> 0]);
                
               session()->put('catalogue_status_import', $fileWithPath) ;
                session()->put('catalogue_status_count', 0) ;

                foreach ($records as $key => $row) 
                {
                    if($key > 0){
                        if(!isset($row[0]) || $row[0] === null || $row[0] === '' || empty(trim($row[0])))
                        {
                            array_push($unprocessedData,"<br> Catalogue code of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        if(!isset($row[2]) || $row[2] === null || $row[2] === '' || empty(trim($row[2])))
                        {
                            array_push($unprocessedData,"<br> Catalogue Point of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                      
                        if(!isset($row[3]) || $row[3] === null || $row[3] === '')
                        {
                            array_push($unprocessedData,"<br>Cataogue Status of row ".($key + 1)." is required");
                            $unProcessedCount++ ; 
                            continue;
                        }
                        if(!Catalogue::checkValidStatusCode($row[3]))
                        {
                            array_push($unprocessedData,"<br>Catalogue Status value of row ".($key + 1)." is invalid");
                            $unProcessedCount++ ; 
                            continue;
                        }

                        $catalogue = Catalogue::where('catalogue_code', $row[0])->first();
                        
                        if(empty($catalogue))
                        {
                            array_push($unprocessedData,"<br>Catalogue code value of row ".($key + 1)." is invalid");
                            $unProcessedCount++ ; 
                            continue;
                        }
                       $oldValues = $catalogue->getOriginal(); ;

                        //After validating Update
                                                        
                        $catalogue->update([
                            'point'  => $row['2'],
                            'status' => $row['3'],
                        ]);

                        $changedValues = $catalogue->getChanges();
                        // $temp[] = $oldValues ;
                        // $temp[] = $changedValues ;
                       // dd($temp);

                        $diff = [] ;

                            foreach ($changedValues as $key => $item) {
                            $diff[$key] = [
                                'old_data' =>  $oldValues[$key],
                                'new_data' =>  $item,
                            ];
                            }

                            // Log Entry.

                            $logData = [
                                'table_id' => $catalogue->id,
                                'model_name' => 'Catalogue',
                                'user_id' => \Auth::user()->id,
                                'request' => json_encode($row),
                                'response' => json_encode($changedValues),
                                'action' => 'Update',
                                'data_updated' => json_encode($diff),
                                'remarks' => 'bulk_upload_edit_point_status',

                            ];

                            $this->createLog($logData) ;

                        $count++ ;
                        session()->put('catalogue_status_count', $count) ;
                            
                    
                
                    }
                }
                $request->session()->forget('catalogue_status_import');

               

                $responseMessage = 'Import Successfull, '.session()->get('catalogue_status_count').' records processed';
                if($unProcessedCount > 0)
                {
                    $responseMessage .= ' & <span class="text-danger">'.$unProcessedCount.' records unprocessed.'.implode(",",$unprocessedData).'</span>';
                }
                else
                {
                    $responseMessage .= '.';
                }
                return response()->json(['success'=> true, 'import_status'=> 1, 'message'=> $responseMessage], 200);
    
            }
    
            
        } catch (\Exception $e) {
            $request->session()->forget('catalogue_status_import');
            return response()->json(['success'=> false, 'import_status'=> 1, 'message'=> 'Error: '.$e->getMessage()], 200);
    
           
        }
    }


    
}
