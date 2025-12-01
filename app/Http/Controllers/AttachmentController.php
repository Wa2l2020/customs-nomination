<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nomination;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Log;

class AttachmentController extends Controller
{
    public function index()
    {
        $nominations = Nomination::select('id', 'employee_name', 'job_number', 'attachments', 'cloud_folder_link')
                                 ->latest()
                                 ->paginate(20);

        // Calculate Stats
        $totalSize = 0;
        $fileCount = 0;
        $archivedCount = 0;
        $linkedCount = 0;

        // Note: Iterating all for stats might be heavy if thousands of records, 
        // but for now it's acceptable. For production with huge data, we'd cache this.
        $allNominations = Nomination::select('attachments', 'cloud_folder_link')->get();
        
        foreach ($allNominations as $nom) {
            if ($nom->cloud_folder_link) {
                $linkedCount++;
            }
            
            if ($nom->attachments) {
                $hasLocal = false;
                foreach ($nom->attachments as $att) {
                    if (isset($att['path']) && Storage::disk('public')->exists($att['path'])) {
                        $totalSize += Storage::disk('public')->size($att['path']);
                        $fileCount++;
                        $hasLocal = true;
                    }
                }
                if (!$hasLocal && !$nom->cloud_folder_link) {
                    // Assuming if no local files and no link, it might be archived or just empty
                    // But "Archived" usually means we downloaded and deleted them.
                    // Let's count "Archived" as those with NO local files but HAD attachments initially?
                    // Hard to track without a flag. 
                    // Let's just count "Linked" and "Total Size".
                }
            }
        }

        $totalSizeMb = round($totalSize / 1024 / 1024, 2);

        return view('admin.attachments.index', compact('nominations', 'totalSizeMb', 'fileCount', 'linkedCount'));
    }

    public function updateLink(Request $request, $id)
    {
        $request->validate([
            'cloud_folder_link' => 'nullable|url'
        ]);

        $nomination = Nomination::findOrFail($id);
        $nomination->cloud_folder_link = $request->cloud_folder_link;
        $nomination->save();

        return response()->json(['success' => true, 'message' => 'تم تحديث الرابط بنجاح']);
    }

    public function archive(Request $request)
    {
        $deleteAfter = $request->has('delete_after');
        
        $zipFileName = 'nominations_archive_' . date('Y-m-d_H-i') . '.zip';
        $zipPath = storage_path('app/public/' . $zipFileName);
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            
            $nominations = Nomination::whereNotNull('attachments')->get();
            $filesAdded = 0;

            foreach ($nominations as $nom) {
                if (empty($nom->attachments)) continue;

                // Create a folder for this candidate inside ZIP
                $folderName = $nom->job_number . '_' . str_replace(' ', '_', $nom->employee_name);
                
                foreach ($nom->attachments as $att) {
                    if (isset($att['path']) && Storage::disk('public')->exists($att['path'])) {
                        $absolutePath = Storage::disk('public')->path($att['path']);
                        $fileName = basename($att['path']);
                        
                        // Add file to ZIP inside candidate folder
                        $zip->addFile($absolutePath, $folderName . '/' . $fileName);
                        $filesAdded++;
                    }
                }
            }
            
            $zip->close();
            
            if ($filesAdded === 0) {
                return back()->with('error', 'لا توجد ملفات للأرشفة.');
            }

            // If Delete Requested
            if ($deleteAfter) {
                foreach ($nominations as $nom) {
                    if (empty($nom->attachments)) continue;
                    foreach ($nom->attachments as $att) {
                        if (isset($att['path']) && Storage::disk('public')->exists($att['path'])) {
                            Storage::disk('public')->delete($att['path']);
                        }
                    }
                    // Optionally remove empty folders
                    // Storage::disk('public')->deleteDirectory('nominations/' . $nom->job_number);
                }
            }

            return response()->download($zipPath)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'فشل إنشاء ملف الأرشيف.');
        }
    }
    public function importLinks(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Read Header
        $header = fgetcsv($handle);
        
        // Normalize header to lowercase for easier matching
        $header = array_map('strtolower', $header);
        $header = array_map('trim', $header);

        $cNumIndex = array_search('c_num', $header);
        $linkIndex = array_search('link', $header);

        if ($cNumIndex === false || $linkIndex === false) {
            fclose($handle);
            return back()->with('error', 'الملف يجب أن يحتوي على الأعمدة: C_num و link');
        }

        $updatedCount = 0;
        $notFoundCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;

            $jobNumber = trim($row[$cNumIndex]);
            $link = trim($row[$linkIndex]);

            if (empty($jobNumber) || empty($link)) continue;

            $nomination = Nomination::where('job_number', $jobNumber)->first();

            if ($nomination) {
                $nomination->cloud_folder_link = $link;
                $nomination->save();
                $updatedCount++;
            } else {
                $notFoundCount++;
            }
        }

        fclose($handle);

        return back()->with('success', "تم تحديث ($updatedCount) رابط بنجاح. لم يتم العثور على ($notFoundCount) رقم حاسب.");
    }
    public function viewFile($folder, $filename)
    {
        $path = 'nominations/' . $folder . '/' . $filename;
        
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return response()->file(Storage::disk('public')->path($path));
    }
}
