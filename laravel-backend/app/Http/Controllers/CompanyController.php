<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Mail\CompanyWelcomeMail;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        return CompanyResource::collection(Company::orderBy('created_at', 'desc')->paginate(10));
    }

    public function store(StoreCompanyRequest $request)
    {
        $filename = '';
        if($request->file('logo')){
            $file = $request->file('logo');
            $content = $file->get();
            $extension =  $file->clientExtension();
            $filename = 'logo_'.rand(1000000,10000000).".".$extension;
            $filepath = 'public/'.$filename;
            Storage::disk('local')->put($filepath, $content);
        }
        
        $company = Company::create([
            'name' => $request->name,
            'email' => $request->email,
            'logo' => $filename,
            'website' => $request->website,
        ]);

        Mail::to($request->email)->send(new CompanyWelcomeMail($company->toArray()));

        return new CompanyResource($company);
    }

    public function update(StoreCompanyRequest $request, Company $company)
    {
        $data_to_update = $request->only(['name', 'email', 'website', 'logo']);
        $filename = '';
        if($request->file('logo')){
            $file = $request->file('logo');
            $content = $file->get();
            $extension =  $file->clientExtension();
            $filename = 'logo_'.rand(1000000,10000000).".".$extension;
            $filepath = 'public/'.$filename;
            Storage::disk('local')->put($filepath, $content);
            $data_to_update['logo'] = $filename;
        }
        
        
        $company->update($data_to_update);

        return new CompanyResource($company);
    }

    public function destroy(Company $company)
    {
        $company->delete();

        return response()->json(null, 204);
    }
}
