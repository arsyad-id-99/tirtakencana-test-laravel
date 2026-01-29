<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Exception;

class CustomerController extends Controller
{
    /**
     * GET: List Semua Customer
     */
    public function index()
    {
        try {
            $customers = Customer::all();
            
            return response()->json([
                'status' => true,
                'message' => 'Data customer berhasil diambil',
                'data' => $customers
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST: Tambah Customer Baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'CustID'     => 'required|unique:customer,CustID|max:11', 
            'Name'       => 'required|max:17',
            'Address'    => 'required|max:37',
            'BranchCode' => 'required|max:3',
            'PhoneNo'    => 'required|max:14',
        ], [
            'CustID.unique' => 'ID Customer ini sudah terdaftar.',
            'CustID.required' => 'ID Customer wajib diisi.',
            'Name.required' => 'Nama Customer wajib diisi.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customer = Customer::create([
                'CustID'     => $request->CustID,
                'Name'       => $request->Name,
                'Address'    => $request->Address,
                'BranchCode' => $request->BranchCode,
                'PhoneNo'    => $request->PhoneNo,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Customer berhasil ditambahkan',
                'data' => $customer
            ], 201); 

        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Database Error: Gagal menyimpan data.',
                // 'error_detail' => $e->getMessage() 
            ], 500);

        } catch (Exception $e) {
            // Error Umum Lainnya
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan server.',
            ], 500);
        }
    }

    /**
     * DELETE: Hapus Customer
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::where('CustID', $id)->first();

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer tidak ditemukan',
                ], 404);
            }

            $customer->delete();

            return response()->json([
                'status' => true,
                'message' => 'Customer berhasil dihapus',
            ], 200);

        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) { 
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal hapus! Customer ini memiliki riwayat transaksi.',
                ], 409); 
            }

            return response()->json([
                'status' => false,
                'message' => 'Database Error: Gagal menghapus data.',
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan server.',
            ], 500);
        }
    }
}