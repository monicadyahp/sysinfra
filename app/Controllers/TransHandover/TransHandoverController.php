<?php

namespace App\Controllers\TransHandover;

use App\Controllers\BaseController; 
use App\Models\transhandover\TransHandoverModel;
use FPDF;

require_once APPPATH . 'ThirdParty/fpdf/fpdf.php';

class TransHandoverController extends BaseController {
    
    protected $TransHandoverModel;
    
    public function __construct()
    {
        $this->TransHandoverModel = new TransHandoverModel();
    }
    
    public function index()
    {
        if (!session()->get('login')) {
            return redirect()->to('/login');
        }
        
        $usermenu = session()->get("usermenu");
        
        $activeMenuGroup = 'Transaction';
        $activeMenuName = 'Handover';
        
        foreach ($usermenu as $menu) {
            if ($menu->umn_path === "TransHandover") {
                $activeMenuGroup = $menu->umg_groupname;
                $activeMenuName = $menu->umn_menuname;
                break;
            }
        }
        
        $data = [
            "active_menu_group" => $activeMenuGroup,
            "active_menu_name" => $activeMenuName,
        ];
        
        return view('transaction/TransHandover/index', $data);
    }
    
    public function getHandoverData()
    {
        $handoverData = $this->TransHandoverModel->getHandoverData();
        
        $formattedData = [];
        foreach ($handoverData as $handover) {
            $formattedData[] = [
                'th_recordno'    => $handover->th_recordno,
                'th_requestdate' => $handover->th_requestdate,
                'th_empno_rep'   => $handover->th_empno_rep,
                'th_empname_rep' => $handover->th_empname_rep,
                'th_sectioncode_rep' => $handover->th_sectioncode_rep,
                'section_name'   => $handover->section_name,
                'th_purpose'     => $handover->th_purpose,
                'th_reason'      => $handover->th_reason,
            ];
        }
        
        return $this->response->setJSON($formattedData);
    }
    
    public function getHandoverDetailData()
    {
        $requestNo = $this->request->getGet('requestNo');
        if (empty($requestNo)) {
            return $this->response->setJSON([]);
        }
        
        $handoverDetails = $this->TransHandoverModel->getHandoverDetailData($requestNo);
        
        return $this->response->setJSON($handoverDetails);
    }
    
    public function getHandoverById()
    {
        $id = $this->request->getGet('id');
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Record number is required']);
        }
        
        $handover = $this->TransHandoverModel->getHandoverById($id);
        
        if ($handover) {
            return $this->response->setJSON([
                'status' => true,
                'data' => $handover
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Handover record not found'
            ]);
        }
    }
    
    public function getHandoverDetailById()
    {
        $id = $this->request->getGet('id');
        
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => 'Detail ID is required'
            ]);
        }
        
        $detail = $this->TransHandoverModel->getHandoverDetailById($id);
        
        if ($detail) {
            return $this->response->setJSON([
                'status' => true,
                'data' => $detail
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Handover detail record not found'
            ]);
        }
    }
    
    public function checkRecordNoExists()
    {
        $recordNo = $this->request->getGet('recordNo');
        
        if (empty($recordNo)) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => 'Record number is required'
            ]);
        }
        
        $exists = $this->TransHandoverModel->checkRecordNoExists($recordNo);
        
        return $this->response->setJSON([
            'status' => true,
            'exists' => $exists
        ]);
    }
    
    public function storeHandover()
    {
        $data = $this->request->getPost();
        
        // Validate input
        if (empty($data['record_no']) || empty($data['request_date']) || empty($data['employee_no']) || 
            empty($data['purpose_content'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'All required fields must be filled out.'
            ]);
        }
        
        // Check if record number exists but is marked as deleted
        $existingRecord = $this->TransHandoverModel->getHandoverById($data['record_no']);
        
        if ($existingRecord && $existingRecord->th_status == 25) {
            // The record exists but is marked as deleted, so we'll reactivate it
            $updateData = [
                'record_no'      => $data['record_no'],
                'request_date'   => $data['request_date'],
                'employee_no'    => $data['employee_no'],
                'employee_name'  => $data['employee_name'],
                'section_code'   => $data['section_code'],
                'purpose_content'   => $data['purpose_content'],
                'reason_content' => $data['reason_content'],
                'status'         => 1  // Explicitly set status to 1 (active)
            ];
            
            $result = $this->TransHandoverModel->updateDeletedRecord($updateData);
        } else {
            // Create a brand new record
            $result = $this->TransHandoverModel->storeHandoverData($data);
        }
        
        return $this->response->setJSON($result);
    }
    
    public function storeHandoverDetail()
    {
        $data = $this->request->getPost();
        
        // Validate input
        if (empty($data['recordno'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Record number is required.'
            ]);
        }
        
        // Check if we're adding by asset or serial number
        $addType = $data['add_type'] ?? 'asset';
        
        if ($addType === 'asset' && empty($data['asset_no'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Asset no is required.'
            ]);
        } else if ($addType === 'serial' && empty($data['serial_number'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Serial number is required.'
            ]);
        }
        
        $result = $this->TransHandoverModel->storeHandoverDetailData($data);
        return $this->response->setJSON($result);
    }
    
    public function updateHandover()
    {
        $data = $this->request->getPost();
        
        // Validate input
        if (empty($data['record_no']) || empty($data['request_date']) || empty($data['employee_no']) || 
            empty($data['purpose_content'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'All required fields must be filled out.'
            ]);
        }
        
        // Get the original record number from hidden field
        $originalRecordNo = $this->request->getPost('original_record_no');
        if ($originalRecordNo) {
            $data['original_record_no'] = $originalRecordNo;
        }
        
        $result = $this->TransHandoverModel->updateHandoverData($data);
        return $this->response->setJSON($result);
    }
    
    public function updateHandoverDetail()
    {
        $data = $this->request->getPost();
        
        // Validate input
        if (empty($data['id'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Detail ID is required.'
            ]);
        }
        
        $result = $this->TransHandoverModel->updateHandoverDetailData($data);
        return $this->response->setJSON($result);
    }
    
    public function deleteHandover()
    {
        $id = $this->request->getPost('id');
        if (empty($id)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Record number not found']);
        }
        
        $deleted = $this->TransHandoverModel->deleteHandoverData($id);
        if ($deleted) {
            return $this->response->setJSON(['status' => true, 'message' => 'Handover has been marked as deleted successfully']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Failed to delete handover']);
        }
    }
    
    public function deleteHandoverDetail()
    {
        $id = $this->request->getPost('id');
        
        if (empty($id)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Detail ID is required'
            ]);
        }
        
        $deleted = $this->TransHandoverModel->deleteHandoverDetailData($id);
        if ($deleted) {
            return $this->response->setJSON(['status' => true, 'message' => 'Handover detail has been marked as deleted successfully']);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Failed to delete handover detail']);
        }
    }
    
    public function searchEmployees()
    {
        $search = $this->request->getGet('search') ?? '';
        $exclude = $this->request->getGet('exclude') ?? '';
        
        $employees = $this->TransHandoverModel->searchEmployees($search, $exclude);
        return $this->response->setJSON($employees);
    }
    
    public function searchSystemEmployees()
    {
        $search = $this->request->getGet('search') ?? '';
        
        $employees = $this->TransHandoverModel->searchSystemEmployees($search);
        return $this->response->setJSON($employees);
    }
    
    public function getEmployeeDetails()
    {
        $employeeId = $this->request->getGet('employeeId');
        
        if (empty($employeeId)) {
            return $this->response->setJSON(['status' => false, 'message' => 'Employee ID is required']);
        }
        
        $employee = $this->TransHandoverModel->getEmployeeById($employeeId);
        
        if ($employee) {
            return $this->response->setJSON(['status' => true, 'data' => $employee]);
        } else {
            return $this->response->setJSON(['status' => false, 'message' => 'Employee not found']);
        }
    }
    
    public function searchAssets()
    {
        $search = $this->request->getGet('search') ?? '';
        
        $assets = $this->TransHandoverModel->searchAssets($search);
        return $this->response->setJSON($assets);
    }
    
    public function getEquipmentByAssetNo()
    {
        $assetNo = $this->request->getGet('assetNo');
        
        if (empty($assetNo)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Asset no is required'
            ]);
        }
        
        $equipment = $this->TransHandoverModel->getEquipmentByAssetNo($assetNo);
        
        if ($equipment) {
            return $this->response->setJSON([
                'status' => true,
                'data' => $equipment
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Equipment not found with this asset no'
            ]);
        }
    }

    public function searchEquipmentBySerialNumber()
    {
        $search = $this->request->getGet('search') ?? '';
        
        $equipment = $this->TransHandoverModel->searchEquipmentBySerialNumber($search);
        return $this->response->setJSON($equipment);
    }
    
    public function getEquipmentBySerialNumber()
    {
        $serialNumber = $this->request->getGet('serialNumber');
        
        if (empty($serialNumber)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Serial number is required'
            ]);
        }
        
        $equipment = $this->TransHandoverModel->getEquipmentBySerialNumber($serialNumber);
        
        if ($equipment) {
            return $this->response->setJSON([
                'status' => true,
                'data' => $equipment
            ]);
        } else {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Equipment not found with this serial number'
            ]);
        }
    }
    
    public function getEquipmentCategories()
    {
        $categories = $this->TransHandoverModel->getEquipmentCategories();
        return $this->response->setJSON($categories);
    }


    public function export_pdf()
    {
        // Membersihkan buffer output untuk memastikan tidak ada output yang tidak diinginkan sebelum pengiriman PDF
        ob_start();
        ob_end_clean();

        // Mengatur header HTTP agar browser menganggap output sebagai file PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="handover_it_equipment.pdf"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        // Membuat objek PDF baru menggunakan library FPDF
        $pdf = new \FPDF();
        $pdf->AddPage();

        // Menetapkan warna latar belakang dan warna teks untuk header
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 8);

        // Menambahkan teks "DOKUMEN RAHASIA | CONFIDENTIAL" yang diposisikan di tengah atas
        $text = 'DOKUMEN RAHASIA | CONFIDENTIAL  ';
        $text_width = $pdf->GetStringWidth($text);
        $page_width = $pdf->GetPageWidth();
        $x_position = ($page_width - $text_width) / 2;
        $pdf->SetX($x_position);
        $pdf->Cell($text_width, 5, $text, 0, 9, 'L', true);

        // Mengatur warna latar belakang menjadi putih dan warna teks menjadi hitam untuk bagian berikutnya
        $pdf->SetFillColor(255, 255, 255); 
        $pdf->SetTextColor(0, 0, 0); 
        
        // Memberikan jarak vertikal dan mengatur font untuk bagian perusahaan dan logo
        $pdf->Ln(2);  
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY(10, $pdf->GetY());         
        $pdf->Cell(50, 10, 'PT JST INDONESIA', 0, 1, 'L');        
        $pdf->Image('assets/img/1.png', 75.5, $pdf->GetY()-13, 5, 5);  

        // Menambahkan judul utama di bagian tengah
        $pdf->Ln(2); 
        $pdf->SetFont('Arial', 'B', 15);
        $pdf->SetXY(10, $pdf->GetY()); 
        $pdf->Cell(5, 3, 'HANDOVER IT EQUIPMENT', 0, 1, 'L');

        // Membuat bagian tabel header untuk System dan Approval/Checked/Prepared
        $pdf->Ln(2); 
        $pdf->SetFont('Arial', '', 10); 
        $pdf->SetXY(10, 40); 
        $pdf->Cell(75, 6, 'System', 1, 0, 'C');
        $pdf->Ln(2); 
        $pdf->SetFont('Arial', '', 10); 
        $pdf->SetXY(10, 46); 
        $pdf->Cell(25, 6, 'Approved', 1, 0, 'C');
        $pdf->Cell(25, 6, 'Checked', 1, 0, 'C');
        $pdf->Cell(25, 6, 'Prepared', 1, 0, 'C');

        // Membuat kotak kosong untuk tanda tangan atau tanda centang di kolom Approval, Checked, Prepared
        $pdf->Ln();  
        $pdf->SetXY(10, $pdf->GetY()); 
        $pdf->Cell(25, 25, '     ', 1, 0, 'C');  
        $pdf->Cell(25, 25, '     ', 1, 0, 'C');  
        $pdf->Cell(25, 25, '     ', 1, 0, 'C');  

        // Menghitung posisi untuk kolom Requester di kanan
        $pdf->SetXY($pdf->GetX() + 20 * 3, 50);
        $cellX = 190; 
        $cellY = 46; 

        // Menghitung posisi X untuk Requester berdasarkan margin kanan
        $widthRequester = 32;
        $pageWidth = $pdf->GetPageWidth();
        $marginRight = 10;
        $xRequester = $pageWidth - $marginRight - $widthRequester;
        $yRequester = 40;

        // Membuat kolom Requester di kanan atas
        $pdf->SetXY($xRequester, $yRequester);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($widthRequester, 12, 'Requester', 1, 0, 'C');

        // Kotak kosong di bawah kolom Requester untuk tanda tangan
        $pdf->Ln();
        $pdf->SetXY($xRequester, $pdf->GetY());
        $pdf->Cell(32, 25, '     ', 1, 0, 'C');

        // Fungsi calculateFixedLines untuk membagi teks menjadi beberapa baris agar tidak melebihi panjang tertentu
        if (!function_exists('calculateFixedLines')) {
            function calculateFixedLines($text, $max = 12) {
                $t   = str_replace(["\r\n","\r","\n"], ' ', $text);
                $len = mb_strlen($t);
                $out = [];
                for ($i = 0; $i < $len; $i += $max) {
                    $out[] = mb_substr($t, $i, $max);
                }
                return $out;
            }
        }

        // Membuat bagian header untuk permintaan tanggal
        $pdf->Ln(30);
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->SetFont('Arial','B',10);

        // Membuat bagian header untuk permintaan tanggal
        $header = ' ';
        $lines  = calculateFixedLines($header, 12);
        $cellW = 30;
        $lineH = 10;
        $totalH = count($lines) * $lineH;

        // Menambahkan Cell kosong untuk membuat ruang
        $pdf->Cell($cellW, $totalH, '', 1, 0);

        // Mendapatkan posisi X dan Y
        $x0 = $pdf->GetX() - $cellW;
        $y0 = $pdf->GetY();

        // Menulis "(dd mm yyyy)" di sebelah kiri dengan posisi X yang sudah disesuaikan
        $pdf->SetXY($x0, $y0);  // Menetapkan posisi X dan Y yang diinginkan
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(1, 15, '(dd mm yyyy)', 0, 0, 'L'); // 'L' untuk rata kiri

        $pdf->SetFont('Arial','B',10);

        // Menyesuaikan posisi X lebih ke kiri
        $pdf->SetXY($x0 + 0, $y0);  // Menggeser posisi X lebih ke kiri (ubah nilai -5 sesuai kebutuhan)
        $pdf->Cell(15, 8, 'Request Date', 0, 0, 'L'); // 'L' untuk rata kiri

        
        // Menulis teks header dalam beberapa baris
        foreach ($lines as $i => $txt) {
            $pdf->SetXY($x0, $y0 + $i * $lineH);
            $pdf->Cell($cellW, $lineH, $txt, 0, 0, 'L');
        }
        
        $pdf->SetXY($x0 + $cellW, $y0);

        // Mengambil data record dari parameter GET dan database
        $recordNo = $this->request->getGet('recordNo');
        $handover = $this->TransHandoverModel->getHandoverById($recordNo);
        $handoverDetails = $this->TransHandoverModel->getHandoverDetailData($recordNo);

        // Menampilkan tanggal request
        $pdf->SetXY(40, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 10);
        $requestDate = $handover->th_requestdate ? date('d M Y', strtotime($handover->th_requestdate)) : '-';
        $pdf->Cell(65, 10, $requestDate, 1, 0, 'L'); 

        // Fungsi untuk membagi teks menjadi dua baris agar muat dalam cell
        function CellTwoLines($pdf, $w, $h, $text, $maxChars = 5, $border = 1, $ln = 0, $align = 'L')
        {
            $txt = str_replace(["\r\n","\r","\n"], ' ', $text);
            $line1 = rtrim(mb_substr($txt, 0, $maxChars));
            $line2 = ltrim(mb_substr($txt, $maxChars));
            $lines = [$line1, $line2];
            $lineH = $h / 2;
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            $pdf->Cell($w, $h, '', $border, 0);
            foreach ($lines as $i => $row) {
                $pdf->SetXY($x0, $y0 + $i * $lineH);
                $pdf->Cell($w, $lineH, $row, 0, 0, $align);
            }
            $pdf->SetXY($x0 + $w, $y0);
        }

        // Membuat bagian header untuk permintaan tanggal
        $pdf->SetXY(105, $pdf->GetY());
        $pdf->SetFont('Arial','B',10);

        // Membuat bagian header untuk permintaan tanggal
        $header = ' ';
        $lines  = calculateFixedLines($header, 12);
        $cellW = 35;
        $lineH = 10;
        $totalH = count($lines) * $lineH;

        // Menambahkan Cell kosong untuk membuat ruang
        $pdf->Cell($cellW, $totalH, '', 1, 0);

        // Mendapatkan posisi X dan Y
        $x0 = $pdf->GetX() - $cellW;
        $y0 = $pdf->GetY();

        // Menulis "(code - name)" di sebelah kiri dengan posisi X yang sudah disesuaikan
        $pdf->SetXY($x0, $y0);  // Menetapkan posisi X dan Y yang diinginkan
        $pdf->SetFont('Arial','B',7);
        $pdf->Cell(1, 15, '(code - name)', 0, 0, 'L'); // 'L' untuk rata kiri

        $pdf->SetFont('Arial','B',10);

        // Menyesuaikan posisi X lebih ke kiri
        $pdf->SetXY($x0 + 0, $y0);  // Menggeser posisi X lebih ke kiri (ubah nilai -5 sesuai kebutuhan)
        $pdf->Cell(15, 8, 'User', 0, 0, 'L'); // 'L' untuk rata kiri


        // Mengambil data user dari database
        $recordNo = $this->request->getGet('recordNo');
        $handover = $this->TransHandoverModel->getHandoverById($recordNo); 
        $handoverDetails = $this->TransHandoverModel->getHandoverDetailData($recordNo);
        
        // Menampilkan nama user yang telah diformat
        $pdf->SetXY(140, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 10);

        // Fungsi untuk memformat nama user agar tidak terlalu panjang dan tetap terbaca
        function format_user_code_name($user) {
            if (strlen($user) > 29) {
                $user_parts = explode(' - ', $user);
                if (count($user_parts) > 1) {
                    $name = $user_parts[1];
                    $name_parts = explode(' ', $name);
                    $formatted_name = '';
                    $char_count = strlen($user_parts[0] . ' - '); 
                    foreach ($name_parts as $part) {
                        if (($char_count + strlen($part)) <= 29) {
                            $formatted_name .= $part . ' ';
                            $char_count += strlen($part) + 1;  
                        } else {
                            $formatted_name .= ucfirst(substr($part, 0, 1)) . '.';
                            break; 
                        }
                    }
                    return $user_parts[0] . ' - ' . trim($formatted_name);
                }
            }
            return $user;  
        }
        $userDetails = ($handover->th_empno_rep && $handover->th_empname_rep) ? $handover->th_empno_rep . ' - ' . $handover->th_empname_rep : '-';
        $formattedUserDetails = format_user_code_name($userDetails);
        $pdf->SetXY(140, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(60, 10, $formattedUserDetails, 1, 0, 'L');

        // Bagian input data record number dan tanggal
        $pdf->Ln(10); 
        $pdf->SetXY(10, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 7, 'Record No. (*)', 1, 0, 'L'); 

        // Mengambil data lagi dari database untuk record number
        $recordNo = $this->request->getGet('recordNo');
        $handover = $this->TransHandoverModel->getHandoverById($recordNo); 
        $handoverDetails = $this->TransHandoverModel->getHandoverDetailData($recordNo);

        // Menampilkan nomor record dan tanggal
        $pdf->SetXY(40, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 10);
        $requestDate = $handover->th_recordno ? $handover->th_recordno : '-';
        $pdf->Cell(65, 7, $requestDate, 1, 0, 'L');

        // Fungsi untuk memotong nama bagian departemen agar tidak terlalu panjang
        function format_request_by_dept($section_name) {
            if (strlen($section_name) > 29) {
                return substr($section_name, 0, 25) . '...';
            }
            return $section_name; 
        }

        // Menampilkan departemen pemohon
        $pdf->SetXY(105, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(35, 7, 'Request By Dept.', 1, 0, 'L'); 
        $pdf->SetFont('Arial', 'B', 10);
        
        // Mengambil data record dari parameter GET dan data dari database
        $recordNo = $this->request->getGet('recordNo');
        $handover = $this->TransHandoverModel->getHandoverById($recordNo);
        $handoverDetails = $this->TransHandoverModel->getHandoverDetailData($recordNo);

        // Mengambil nama bagian/departemen
        $sectionName = $handover->section_name ? $handover->section_name : '-';
        $formattedSectionName = format_request_by_dept($sectionName);
        $pdf->Cell(60, 7, $formattedSectionName, 1, 0, 'L');
        
        // Menambah jarak dan memulai bagian berikutnya
        $pdf->Ln(6.9); 
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 7, 'Purpose', 1, 0, 'L'); 

        // Mengambil data record ulang untuk bagian Purpose
        $recordNo = $this->request->getGet('recordNo');
        $handover = $this->TransHandoverModel->getHandoverById($recordNo); 
        $handoverDetails = $this->TransHandoverModel->getHandoverDetailData($recordNo);

        // Fungsi untuk memotong teks purpose agar tidak terlalu panjang
        function format_purpose($purpose) {
            if (strlen($purpose) > 91) {
                return substr($purpose, 0, 88) . '...';
            }
            return $purpose; 
        }

        // Menampilkan purpose yang telah diformat
        $pdf->SetXY(40, $pdf->GetY()); 
        $formattedPurpose = format_purpose($handover->th_purpose);  
        $pdf->SetFont('Arial', 'B', 10);  
        $pdf->Cell(160, 7, $formattedPurpose, 1, 0, 'L');  

        // Fungsi untuk mendapatkan tinggi teks dalam cell (opsional, bisa digunakan untuk penyesuaian)
        function getTextHeight($pdf, $text, $w, $fontSize = 10) {
            $pdf->SetFont('Arial', '', $fontSize);
            return $pdf->GetStringHeight($w, $text);
        }

        // Fungsi menampilkan alasan ketidaksesuaian (discrepancy reason) dalam dua baris
        function CellReasonDiscrepancy($pdf, $w, $h, $text, $maxChars = 9, $border = 1, $ln = 0, $align = 'L') {
            $txt = str_replace(["\r\n", "\r", "\n"], ' ', $text);
            $line1 = rtrim(mb_substr($txt, 0, $maxChars));
            $line2 = ltrim(mb_substr($txt, $maxChars));
            $lines = [$line1, $line2];
            $lineH = $h / 2;
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            $pdf->Cell($w, $h, '', $border, $ln);
            foreach ($lines as $i => $row) {
                $pdf->SetXY($x0, $y0 + $i * $lineH);
                $pdf->Cell($w, $lineH, $row, 0, 0, $align);
            }
            $pdf->SetXY($x0 + $w, $y0);
        }

        // Menampilkan label "Reason of Discrepancy"
        $pdf->Ln(7);
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->SetFont('Arial','B',10);
        CellReasonDiscrepancy($pdf, 30, 10, 'Reason of Discrepancy', 9, 1, 0, 'L');

        // Mengambil data record lagi untuk alasan ketidaksesuaian
        $recordNo = $this->request->getGet('recordNo');
        $handover = $this->TransHandoverModel->getHandoverById($recordNo); 
        $handoverDetails = $this->TransHandoverModel->getHandoverDetailData($recordNo);

        $pdf->SetXY(40, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 10);

        // Fungsi untuk memformat alasan agar tidak terlalu panjang dan tetap terbaca
        function format_reason($reason, $maxLen = 89) {
            $txt = str_replace(["\r\n", "\r", "\n"], ' ', $reason);
            if (mb_strlen($txt) <= $maxLen) {
                return [$txt];
            }
            $line1 = mb_substr($txt, 0, $maxLen) . '-';
        
            $rest = mb_substr($txt, $maxLen);
        
            if (mb_strlen($rest) > $maxLen) {
                $line2 = mb_substr($rest, 0, $maxLen) . '...';
            } else {
                $line2 = $rest;
            }
            return [$line1, $line2];
        }
        $lines = format_reason($handover->th_reason);
        $text = implode("\n", $lines);
        $width = 160;
        $totalHeight = 10; 
        $lineHeight = $totalHeight / count($lines); 

        // Menampilkan alasan ketidaksesuaian dalam MultiCell agar otomatis membungkus teks
        $pdf->SetXY(40, $pdf->GetY());
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->MultiCell($width, $lineHeight, $text, 1, 'L');

        // Menambahkan bagian Equipment Requested
        $pdf->Ln(1);  
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(14, 28, 'No.', 0, 0, 'L'); // 'L' untuk rata kiri
        $pdf->Cell(43, 28, 'Equipment Name', 0, 0, 'L'); // 'L' untuk rata kiri
        $pdf->Cell(38, 28, 'Serial Number', 0, 0, 'L'); // 'L' untuk rata kiri
        $pdf->Cell(2, 43, 'Date', 0, 0, 'L'); // 'L' untuk rata kiri
        $pdf->SetFont('Arial', 'B', 8);

        // Menentukan posisi baru untuk (dd mm yyyy), lebih ke kiri
        $xOffset = -7; // Sesuaikan nilai ini sesuai kebutuhan (lebih kecil = lebih kiri)

        $pdf->SetXY($pdf->GetX() + $xOffset, $pdf->GetY());  // Memindahkan posisi lebih ke kiri
        $pdf->Cell(23.5, 50, '(dd mm yyyy)', 0, 0, 'L'); // Menulis "(dd mm yyyy)" lebih ke kiri
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(14, 43, 'SIC', 0, 0, 'L'); // 'L' untuk rata kiri
        $pdf->Cell(18, 43, 'User', 0, 0, 'L'); // 'L' untuk rata kiri
        $pdf->Cell(1, 43, 'Date', 0, 0, 'L'); // 'L' untuk rata kiri
        // $pdf->Cell(1, 43, 'SIC', 0, 0, 'L'); // 'L' untuk rata kiri
        $pdf->SetFont('Arial', 'B', 8);
        
        // Menentukan posisi baru untuk "(dd mm yyyy)", lebih ke kiri
        $xOffset = -6.5; // Sesuaikan nilai ini untuk memindahkan lebih ke kiri atau ke kanan (nilai negatif untuk kiri)
        
        $pdf->SetXY($pdf->GetX() + $xOffset, $pdf->GetY());  // Memindahkan posisi lebih ke kiri
        $pdf->Cell(10, 50, '(dd mm yyyy)', 0, 0, 'L'); // Menulis "(dd mm yyyy)" lebih ke kiri
        
        $pdf->SetFont('Arial', 'B', 10);
        
        // Menentukan posisi baru untuk "(dd mm yyyy)", lebih ke kiri
        $xOffset = 13.5; // Sesuaikan nilai ini untuk memindahkan lebih ke kiri atau ke kanan (nilai negatif untuk kiri)
        
        $pdf->SetXY($pdf->GetX() + $xOffset, $pdf->GetY());  // Memindahkan posisi lebih ke kiri
        $pdf->Cell(14, 43, 'SIC', 0, 0, 'L'); // Menulis "(dd mm yyyy)" lebih ke kiri
        $pdf->Cell(14, 43, 'User', 0, 0, 'L'); // Menulis "(dd mm yyyy)" lebih ke kiri

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(10, $pdf->GetY()); 
        $pdf->Cell(50, 10, 'Equipment Requested', 0, 1, 'L');

        // Membuat header tabel equipment list
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(8, 18, '', 1, 0, 'C');
        $pdf->Cell(42, 18, '', 1, 0, 'C');
        $pdf->Cell(40, 18, '', 1, 0, 'C');
        $pdf->Cell(50, 8, 'Delivered', 1, 0, 'C');
        $pdf->SetXY(100, $pdf->GetY() + 8);  
        $pdf->Cell(20, 10, '', 1, 0, 'C');
        $pdf->SetXY(99, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 8); 
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(120, $pdf->GetY()); 
        $pdf->Cell(15, 10, '', 1, 0, 'C');
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(135, $pdf->GetY());  
        $pdf->Cell(15, 10, '', 1, 0, 'C');
        $pdf->SetXY(150, $pdf->GetY() - 8);  
        $pdf->Cell(50, 8, 'Returned', 1, 0, 'C');
        $pdf->SetXY(150, $pdf->GetY() + 8);
        $pdf->Cell(20, 10, '', 1, 0, 'C');
        $pdf->SetXY(149, $pdf->GetY()); 
        $pdf->SetFont('Arial', 'B', 8); 
        $pdf->Cell(22, 16, '', 0, 0, 'C'); 
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(170, $pdf->GetY()); 
        $pdf->Cell(15, 10, '', 1, 0, 'C');
        $pdf->SetXY(185, $pdf->GetY());  
        $pdf->Cell(15, 10, '', 1, 0, 'C');
        
        // Menyiapkan data baris-baris equipment request
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 8);
        $handover = $this->TransHandoverModel->getHandoverById($recordNo);
        $totalData = count($handoverDetails);
        $rowsToFill = 11 - $totalData;  // Menambah baris kosong jika data kurang dari 11
        $counter = 1;

        // Fungsi untuk membagi teks panjang menjadi beberapa baris agar muat dalam cell        
        if (!function_exists('calculateLines')) {
          function calculateLines($pdf,$text,$w){
            $words = explode(' ',$text); $lines=[]; $cur='';
            foreach($words as $wrd){
              $t = $cur===''? $wrd : $cur.' '.$wrd;
              if ($pdf->GetStringWidth($t) <= $w) {
                $cur = $t;
              } else {
                $lines[] = $cur;
                $cur = $wrd;
              }
            }
            if ($cur!=='') $lines[] = $cur;
            return $lines;
          }
        }
        // Fungsi untuk membagi serial number menjadi potongan kecil
        if (!function_exists('calculateSerialLines')) {
            function calculateSerialLines($text, $max = 15) {  
                $t = str_replace(["\r\n", "\r", "\n"], ' ', $text); 
                $len = strlen($t);
                $out = [];
                for ($i = 0; $i < $len; $i += $max) {
                    $out[] = substr($t, $i, $max); 
                }
                return $out; 
            }
        }
        // Fungsi untuk memformat nama agar tidak terlalu panjang dan tetap terbaca
        if (!function_exists('format_name')) {
            function format_name($nm) {
                if (empty($nm)) { // Tambahkan kondisi ini untuk memeriksa apakah $nm kosong
                    return '';
                }

                if (mb_strlen($nm) <= 7) {
                    return $nm;
                }
        
                $chars = mb_str_split($nm);
                $output = '';
                $spaceCount = 0;
        
                for ($i = 0; $i < count($chars); $i++) {
                    $c = $chars[$i];
        
                    if ($spaceCount == 0) {
                        $output .= $c;
                    } else if ($spaceCount == 1) {
                        if (ctype_alpha($c)) {
                            $output .= ' ' . $c . '.';
                            break;
                        } else if ($c === ' ') {
                            continue;
                        } else {
                            $output .= $c;
                        }
                    } else {
                        break;
                    }
        
                    if ($c === ' ') {
                        $spaceCount++;
                    }
        
                    if (mb_strlen($output) >= 7) {
                        break;
                    }
                }
        
                return trim($output);
            }
        }
    
        // Mengisi data baris untuk setiap item equipment request
        $pdf->SetFont('Arial','',10);
        $lineH = 6;
        $cw = [
            'no'=>8,
            'equip_name'=>42,
            'serial'=>40,
            'deliv_date'=>20,
            'deliv_sic'=>15,   
            'deliv_user'=>15,  
            'return_date'=>20,
            'return_sic'=>15,  
            'return_user'=>15, 
        ];

        $i=1;
        foreach ($handoverDetails as $d) {
            // Mengatur font menjadi 9
            $pdf->SetFont('Arial', '', 9);
            
            // Menentukan ukuran tinggi cell yang lebih kecil agar jarak antar baris lebih rapat
            $lineH = 4; // Sesuaikan dengan ukuran yang lebih kecil untuk gap antar baris
            
            // Menghitung jumlah baris teks untuk nama equipment dan serial number
            $eL = calculateLines($pdf, $d->hd_equipmentname, $cw['equip_name']);
            $sL = calculateSerialLines($d->hd_serialnumber, 15);  
            $maxL = max(count($eL), count($sL));
            $rh = $maxL * $lineH;  // Menghitung tinggi baris yang lebih kecil
            
            // Menulis nomor urut
            $pdf->Cell($cw['no'], $rh, $i++, 1, 0, 'C');
            
            // Menyimpan posisi awal
            $x0 = $pdf->GetX();
            $y0 = $pdf->GetY();
            
            // Menulis nama equipment dan serial number secara berbaris
            $totalTextHeight = count($eL) * $lineH;
            $offsetY = ($rh - $totalTextHeight) / 2;
        
            foreach ($eL as $ln => $txt) {
                $pdf->SetXY($x0, $y0 + $ln * $lineH + $offsetY);  
                $pdf->Cell($cw['equip_name'], $lineH, $txt, 0, 0, 'L');  
            }
            // Menggambar kotak di sekitar data equipment
            $pdf->Rect($x0, $y0, $cw['equip_name'], $rh);
            $pdf->SetXY($x0 + $cw['equip_name'], $y0);
            
            // Menulis serial number
            $xs = $pdf->GetX();
            $ys = $pdf->GetY();
            $snLines = count($sL);
            $snHeight = $snLines * $lineH;
            $offsetY = ($rh - $snHeight) / 2;   
            
            foreach ($sL as $ln => $txt) {
                $pdf->SetXY($xs, $ys + $offsetY + $ln * $lineH);
                $pdf->Cell($cw['serial'], $lineH, $txt, 0, 0, 'C');
            }
            // Menggambar kotak untuk serial number
            $pdf->Rect($xs, $ys, $cw['serial'], $rh);
            $pdf->SetXY($xs + $cw['serial'], $ys);
            
            // Menulis tanggal, SIC, User untuk Delivered dan Returned
            $pdf->Cell($cw['deliv_date'], $rh,
                $d->hd_delivereddate ? date('d/m/Y', strtotime($d->hd_delivereddate)) : '-', 1, 0, 'C');
            $pdf->Cell($cw['deliv_sic'], $rh, format_name($d->hd_deliveredname_rep), 1, 0, 'C');
            $pdf->Cell($cw['deliv_user'], $rh, format_name($handover->th_empname_rep), 1, 0, 'C');
            
            // *** Bagian yang diperbaiki ***
            // Untuk kolom "Return Date"
            $pdf->Cell($cw['return_date'], $rh,
                $d->hd_returneddate ? date('d/m/Y', strtotime($d->hd_returneddate)) : '', 1, 0, 'C');
            
            // Untuk kolom "Return SIC"
            // Pastikan Anda memiliki kolom 'hd_returnedname_rep' di model Anda
            $pdf->Cell($cw['return_sic'], $rh, format_name($d->hd_returnedname_rep ?? ''), 1, 0, 'C');
            
            // Untuk kolom "Return User"
            // Pastikan Anda memiliki kolom 'hd_returnedname_user' di model Anda (atau sesuaikan jika nama kolom berbeda)
            $pdf->Cell($cw['return_user'], $rh, format_name($d->hd_returnedname_user ?? ''), 1, 0, 'C');
            // *** Akhir Bagian yang diperbaiki ***
            
            $pdf->Ln($rh);
        }
        
        
        // Menghitung total tinggi data equipment untuk menentukan jumlah baris kosong yang perlu diisi
        $paperHeight = 145; 
        $marginTop = 20; 
        $marginBottom = 10; 
        $usableHeight = $paperHeight - $marginTop - $marginBottom; 
        $lineHeight = $lineH; 

        $totalDataHeight = 0;
        foreach ($handoverDetails as $d) {
            $eL = calculateLines($pdf, $d->hd_equipmentname, $cw['equip_name']);
            $sL = calculateSerialLines($d->hd_serialnumber, 15);
            $maxL = max(count($eL), count($sL));
            $rh = $maxL * $lineH; 

            $totalDataHeight += $rh; 
        }

        // Menghitung sisa ruang kosong dan mengisi baris kosong agar tabel tampak rapi
        $remainingHeight = $usableHeight - $totalDataHeight;
        $emptyRows = floor($remainingHeight / $lineHeight);
        $fill = max(0, $emptyRows); 

        for ($k = 0; $k < $fill; $k++) {
            $h = $lineH;
            $pdf->Cell($cw['no'], $h, '', 1, 0, 'C');
            $pdf->Cell($cw['equip_name'], $h, '', 1, 0, 'C');
            $pdf->Cell($cw['serial'], $h, '', 1, 0, 'C');
            $pdf->Cell($cw['deliv_date'], $h, '', 1, 0, 'C');
            $pdf->Cell($cw['deliv_sic'], $h, '', 1, 0, 'C');
            $pdf->Cell($cw['deliv_user'], $h, '', 1, 0, 'C');
            $pdf->Cell($cw['return_date'], $h, '', 1, 0, 'C');
            $pdf->Cell($cw['return_sic'], $h, '', 1, 0, 'C');
            $pdf->Cell($cw['return_user'], $h, '', 1, 0, 'C');
            $pdf->Ln($h);
        }

        // Penutup bagian tabel dan catatan footer
        $pdf->Ln(2);
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->SetFont('Arial', '', 8);  // Mengatur ukuran font menjadi 6
        $pdf->Cell(50, 10, '(*) fill by System Dept.', 0, 0, 'L');

        // Menetapkan posisi untuk "Form Sheet"
        $pdf->SetXY($pdf->GetPageWidth() - 60, $pdf->GetY());
        $pdf->Cell(50, 10, 'Form Sheet : SY-037 R1', 0, 1, 'R');

    
        // Menghasilkan file PDF dan mengirim ke output browser
        $pdf->Output('I','handover_it_equipment.pdf');
        exit();
    }
}