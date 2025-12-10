<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Smalot\PdfParser\Parser;

class DocumentController extends Controller
{
    // 1. Page Dikhana (Saath mein Files ki list bhi)
    public function index()
    {
        // Saari files nikalo (Nayi wali sabse upar)
        $documents = Document::latest()->get();
        return view('docuchat', ['documents' => $documents]);
    }

    // 2. Upload Logic (Ab purana delete nahi karega)
    public function upload(Request $request)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:10000', // Max 10MB
        ]);

        if ($request->file('pdf_file')) {
            $file = $request->file('pdf_file');
            $filename = $file->getClientOriginalName();

            // Text Nikalo
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getPathname());
            $text = $pdf->getText();

            // 🔥 YAHAN CHANGE KIYA: Truncate hata diya
            // Document::truncate(); <--- YE LINE GAYAB KAR DI
            
            Document::create([
                'filename' => $filename,
                'content' => $text
            ]);

            return back()->with('success', 'File saved! Database updated.');
        }

        return back()->with('error', 'Upload failed.');
    }

    // 3. Chat Logic
    public function askPdf(Request $request)
    {
        $userQuestion = $request->input('message');

        // 🔥 CHANGE: Hamesha LATEST file se baat karo
        $document = Document::latest()->first(); 

        if (!$document) {
            return response()->json(['status' => 'error', 'reply' => 'No PDF found! Upload one first.']);
        }

        // Limit content length for AI memory
        $pdfContent = substr($document->content, 0, 15000); 

        // AI Port Check kar lena (Terminal m dekh ke)
        $url = 'http://127.0.0.1:65175/v1/chat/completions'; 

        $systemPrompt = "
        You are an AI Document Assistant.
        Current Document: $document->filename
        Content:
        $pdfContent

        INSTRUCTIONS:
        Answer strictly based on the provided content. Keep it short.
        ";

        $data = [
            'model' => 'Phi-3.5-mini-instruct-generic-cpu:1',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userQuestion],
            ],
            'stream' => false
        ];

        // cURL Request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        $aiReply = $result['choices'][0]['message']['content'] ?? 'Error processing request.';

        return response()->json(['status' => 'success', 'reply' => $aiReply]);
    }
}