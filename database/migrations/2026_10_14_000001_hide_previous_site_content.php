<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Take the previous sites' content off the Rainbow Automation website (nothing is deleted — rows are set inactive
// and can be re-activated in the admin):
//  - pages of the National Liver Foundation of Bangladesh (NLFB) site and the garment pages (Woven, Fine Knits, Knitwear)
//  - the NLFB branches of the old header menu and menu items that link to those pages
//  - placeholder / previous-company details in Admin → Setting → General and the "Company Name" mail sender name
return new class extends Migration
{
    private $pageTemplates = [
        'Liver Function Test', 'What Is the Liver', 'Hepatitis Basics', 'Hepatitis A', 'Hepatitis B', 'Hepatitis C', 'Hepatitis D',
        'Hepatitis E', 'Official Message', 'Mission Statement', 'Goals & Objectives', 'Executive Committee', 'Founder Members',
        'Life Members', 'Contributors', 'Donations', 'PMTCT', 'Fatty Liver', 'Alcohol Related Liver Disease', 'Drug Induced Liver Disease',
        'Jaundice During Pregnancy', 'Jaundice', 'Liver Stones', 'Liver Tumors', 'Neonatal Jaundice', 'Liver Cancer', 'Bile Duct Injury',
        'Bile Duct Stones', 'Choledochal Cyst', 'Gallbladder Cancer', 'Gallbladder Stones', 'Pancreatic Stones', 'Pancreatic Tumors',
        'Pancreatitis', 'Liver Cirrhosis', 'Biliary Reconstruction', 'Liver Resection', 'Pancreatic Resection', 'Liver Transplantation',
        'Essential Words Related to Liver', 'Liver And Food', 'Living With Liver Disease',
    ];

    private $pageSlugs = ['woven', 'fine-knits', 'knitwear'];

    private $menuRoots = ['About NLFB', 'About liver'];

    // Admin → Setting → General values that belong to the template / a previous company, cleared only while unchanged
    private $generalPlaceholders = [
        'website' => 'https://democompany.com',
        'email' => 'info@companyname.com',
        'mobile' => '01700985XXX',
    ];

    public function up(): void
    {
        $now = now();

        // Pages
        $pageIds = DB::table('posts')->where('type', 0)->where('status', 'active')
            ->where(function ($q) {
                $q->whereIn('template', $this->pageTemplates)->orWhereIn('slug', $this->pageSlugs);
            })->pluck('id');
        DB::table('posts')->whereIn('id', $pageIds)->update(['status' => 'inactive', 'updated_at' => $now]);

        // Menu items (type 8): the NLFB branches with everything below them, and links to the pages above
        $menuIds = DB::table('attributes')->where('type', 8)->whereIn('name', $this->menuRoots)->pluck('id')->all();
        for ($level = DB::table('attributes')->whereIn('parent_id', $menuIds)->where('type', 8)->pluck('id')->all(); $level; ) {
            $menuIds = array_merge($menuIds, $level);
            $level = DB::table('attributes')->where('type', 8)->whereIn('parent_id', $level)->whereNotIn('id', $menuIds)->pluck('id')->all();
        }
        $linkIds = DB::table('attributes')->where('type', 8)->where('menu_type', 1)->whereIn('src_id', $pageIds)->pluck('id')->all();
        DB::table('attributes')->whereIn('id', array_unique(array_merge($menuIds, $linkIds)))->where('status', 'active')
            ->update(['status' => 'inactive', 'updated_at' => $now]);

        // General settings
        $general = DB::table('generals')->first();
        if ($general) {
            $update = [];
            foreach ($this->generalPlaceholders as $column => $placeholder) {
                if ($general->$column === $placeholder) {
                    $update[$column] = null;
                }
            }
            // previous company's office address (Uttara) — Rainbow Automation's offices are in Admin → Contact Locations
            if ($general->address_one && str_starts_with($general->address_one, 'Suite: B2, Level: 03')) {
                $update['address_one'] = null;
            }
            if ($general->mail_from_name === 'Company Name') {
                $update['mail_from_name'] = $general->title ?: 'Rainbow Automation';
            }
            if ($update) {
                DB::table('generals')->where('id', $general->id)->update($update + ['updated_at' => $now]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
