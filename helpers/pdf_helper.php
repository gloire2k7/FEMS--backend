<?php

class PDFHelper
{
    /**
     * Generate Inspection Label PDF
     * Requires TCPDF library
     */
    public static function generateLabel($extinguisherId, $data)
    {
        $filename = 'label_' . $data['serial_number'] . '.pdf';
        $path = __DIR__ . '/../uploads/labels/' . $filename;

        // Ensure directory exists
        $dir = dirname($path);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        // Logic check: if TCPDF exists, use it. Otherwise, use a placeholder.
        if (class_exists('TCPDF')) {
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('FEMS System');
            $pdf->SetTitle('Inspection Label - ' . $data['serial_number']);
            $pdf->SetMargins(10, 10, 10);
            $pdf->AddPage('P', array(100, 150)); // Custom label size

            $html = '
                <div style="text-align:center; border: 1px solid #000; padding: 10px;">
                    <h1 style="font-size: 16px;">FIRE EXTINGUISHER LABEL</h1>
                    <p><b>Serial:</b> ' . $data['serial_number'] . '</p>
                    <p><b>Type:</b> ' . $data['type'] . ' (' . $data['capacity'] . ')</p>
                    <p><b>Client:</b> ' . $data['client_name'] . '</p>
                    <hr>
                    <p><b>Filling Date:</b> ' . $data['filling_date'] . '</p>
                    <p><b>Expiry Date:</b> ' . $data['expiry_date'] . '</p>
                    <div style="margin-top: 20px;">
                        [QR CODE PLACEHOLDER]
                    </div>
                </div>
            ';

            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output($path, 'F');
        }
        else {
            // Create a valid-ish looking text file for now if TCPDF is missing
            $content = "-------------------------------------------\n";
            $content .= "       FIRE EXTINGUISHER LABEL\n";
            $content .= "-------------------------------------------\n";
            $content .= "Serial: " . $data['serial_number'] . "\n";
            $content .= "Type:   " . $data['type'] . " (" . $data['capacity'] . ")\n";
            $content .= "Client: " . $data['client_name'] . "\n";
            $content .= "-------------------------------------------\n";
            $content .= "Filling: " . $data['filling_date'] . "\n";
            $content .= "Expiry:  " . $data['expiry_date'] . "\n";
            $content .= "-------------------------------------------\n";
            $content .= "Scan the QR code to view inspection history.\n";
            
            file_put_contents($path, $content);
        }

        return '/uploads/labels/' . $filename;
    }
}
