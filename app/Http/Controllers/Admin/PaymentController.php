<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Candidate;
use App\Models\Payment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['candidate', 'pack'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('operator')) {
            $query->where('operator', $request->operator);
        }
        if ($request->filled('candidate_id')) {
            $query->where('candidate_id', $request->candidate_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments   = $query->paginate(25)->withQueryString();
        $candidates = Candidate::orderBy('number')->get();

        $stats = [
            'total'   => Payment::count(),
            'success' => Payment::success()->count(),
            'pending' => Payment::pending()->count(),
            'failed'  => Payment::failed()->count(),
            'revenue' => Payment::success()->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'candidates', 'stats'));
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Payment::with(['candidate', 'pack'])->latest();

        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('operator'))     $query->where('operator', $request->operator);
        if ($request->filled('candidate_id')) $query->where('candidate_id', $request->candidate_id);

        $payments = $query->get();

        $filename = 'paiements_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($handle, ['ID', 'Date', 'Candidate', 'Pack', 'Opérateur', 'Téléphone', 'Montant (FCFA)', 'Votes', 'Statut', 'Référence'], ';');

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->id,
                    $p->created_at->format('d/m/Y H:i'),
                    $p->candidate->name ?? '-',
                    $p->pack->name ?? '-',
                    strtoupper($p->operator),
                    $p->phone_number,
                    $p->amount,
                    $p->votes_count,
                    $p->status_label,
                    $p->transaction_ref ?? '-',
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}