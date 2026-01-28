<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerTTH;
use App\Models\CustomerTTHDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = CustomerTTH::with(['customer:CustID,Name', 'details'])
            ->get()
            ->map(function ($item) {
                return [
                    'tth_no' => $item->TTHNo,
                    'ttottp_no' => $item->TTOTTPNo,
                    'shop_name' => $item->customer->Name ?? 'Unknown', 
                    'prizes' => $item->details->map(function ($detail) { 
                        return $detail->Jenis . ' (' . $detail->Qty . ' ' . $detail->Unit . ')';
                    })
                ];
            });

        return response()->json($transactions);
    }

    public function show($ttottpNo)
    {
        $details = CustomerTTHDetail::where('TTOTTPNo', $ttottpNo)->get();
        return response()->json($details);
    }

    public function store(Request $request)
    {
        $request->validate([
            'TTHNo' => 'required',
            'TTOTTPNo' => 'required|unique:dbo.CustomerTTH,TTOTTPNo',
            'CustID' => 'required|exists:dbo.Customer,CustID',
            'SalesID' => 'required',
            'details' => 'required|array',
            'details.*.Jenis' => 'required',
            'details.*.Qty' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            $tth = CustomerTTH::create([
                'TTHNo' => $request->TTHNo,
                'SalesID' => $request->SalesID,
                'TTOTTPNo' => $request->TTOTTPNo,
                'CustID' => $request->CustID,
                'DocDate' => now(),
                'Received' => 0
            ]);

            foreach ($request->details as $item) {
                $unit = $this->determineUnit($item['Jenis']);
                
                CustomerTTHDetail::create([
                    'TTHNo' => $request->TTHNo,
                    'TTOTTPNo' => $request->TTOTTPNo,
                    'Jenis' => $item['Jenis'],
                    'Qty' => $item['Qty'],
                    'Unit' => $unit
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'TTH & Details created successfully'], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function updateDetail(Request $request, $id)
    {
        $detail = CustomerTTHDetail::find($id);
        if (!$detail) return response()->json(['message' => 'Detail not found'], 404);

        $request->validate([
            'Jenis' => 'required',
            'Qty' => 'required|integer'
        ]);

        $newUnit = $this->determineUnit($request->Jenis);

        $detail->update([
            'Jenis' => $request->Jenis,
            'Qty' => $request->Qty,
            'Unit' => $newUnit
        ]);

        return response()->json(['message' => 'Detail updated', 'data' => $detail]);
    }

    public function destroy($ttottpNo)
    {
        CustomerTTHDetail::where('TTOTTPNo', $ttottpNo)->delete();
        $deleted = CustomerTTH::where('TTOTTPNo', $ttottpNo)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Transaction deleted']);
        }
        return response()->json(['message' => 'Not found'], 404);
    }

    private function determineUnit($jenis)
    {
        if (stripos($jenis, 'Emas') !== false) {
            return 'Buah';
        } elseif (stripos($jenis, 'Voucher') !== false) {
            return 'Lembar';
        }
        return 'Pcs'; 
    }
}