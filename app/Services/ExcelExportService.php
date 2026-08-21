<?php

namespace App\Services;

class ExcelExportService
{
    /**
     * Export data to Microsoft Excel XML Format (.xls / .xlsx compatible)
     * Compatible with Microsoft Excel 2003-365, WPS Office, LibreOffice, & Google Sheets
     */
    public static function exportToExcel(string $filename, array $headers, array $rows, string $sheetTitle = 'Data Responden'): void
    {
        $cleanFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename) . '_' . date('Ymd_His') . '.xls';

        if (!headers_sent()) {
            header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
        ?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Title><?= htmlspecialchars($sheetTitle, ENT_XML1, 'UTF-8') ?></Title>
  <Author>ASR FORM</Author>
  <Created><?= date('Y-m-d\TH:i:s\Z') ?></Created>
 </DocumentProperties>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Color="#0F172A"/>
  </Style>
  <Style ss:ID="TitleStyle">
   <Alignment ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="14" ss:Bold="1" ss:Color="#1E293B"/>
  </Style>
  <Style ss:ID="SubtitleStyle">
   <Alignment ss:Vertical="Center"/>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Italic="1" ss:Color="#64748B"/>
  </Style>
  <Style ss:ID="HeaderStyle">
   <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#334155"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#334155"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>
   <Interior ss:Color="#2563EB" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="DataRow">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1E293B"/>
  </Style>
  <Style ss:ID="DataRowAlt">
   <Alignment ss:Vertical="Center"/>
   <Borders>
    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
   </Borders>
   <Font ss:FontName="Segoe UI" ss:Size="9" ss:Color="#1E293B"/>
   <Interior ss:Color="#F8FAFC" ss:Pattern="Solid"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="<?= htmlspecialchars(substr($sheetTitle, 0, 30), ENT_XML1, 'UTF-8') ?>">
  <Table ss:DefaultRowHeight="20">
   <Column ss:Width="40"/>
   <Column ss:Width="130"/>
   <?php foreach (array_slice($headers, 2) as $h): ?>
    <Column ss:Width="160"/>
   <?php endforeach; ?>

   <!-- Title Row -->
   <Row ss:Height="26">
    <Cell ss:StyleID="TitleStyle" ss:MergeAcross="<?= max(0, count($headers) - 1) ?>">
     <Data ss:Type="String"><?= htmlspecialchars($sheetTitle, ENT_XML1, 'UTF-8') ?></Data>
    </Cell>
   </Row>
   <Row ss:Height="18">
    <Cell ss:StyleID="SubtitleStyle" ss:MergeAcross="<?= max(0, count($headers) - 1) ?>">
     <Data ss:Type="String">Diekspor pada: <?= date('d F Y, H:i:s') ?> WIB &bull; Sistem ASR FORM</Data>
    </Cell>
   </Row>
   <Row ss:Height="8"/>

   <!-- Table Header -->
   <Row ss:Height="24">
    <?php foreach ($headers as $header): ?>
     <Cell ss:StyleID="HeaderStyle">
      <Data ss:Type="String"><?= htmlspecialchars((string)$header, ENT_XML1, 'UTF-8') ?></Data>
     </Cell>
    <?php endforeach; ?>
   </Row>

   <!-- Data Rows -->
   <?php foreach ($rows as $rowIndex => $row): ?>
    <?php $style = ($rowIndex % 2 === 0) ? 'DataRow' : 'DataRowAlt'; ?>
    <Row ss:Height="20">
     <?php foreach ($row as $cell): ?>
      <?php
        $cellVal = (string)$cell;
        $isNumeric = is_numeric($cellVal) && !str_starts_with($cellVal, '0') && strlen($cellVal) < 12;
      ?>
      <Cell ss:StyleID="<?= $style ?>">
       <?php if ($isNumeric): ?>
        <Data ss:Type="Number"><?= $cellVal ?></Data>
       <?php else: ?>
        <Data ss:Type="String"><?= htmlspecialchars($cellVal, ENT_XML1, 'UTF-8') ?></Data>
       <?php endif; ?>
      </Cell>
     <?php endforeach; ?>
    </Row>
   <?php endforeach; ?>

  </Table>
 </Worksheet>
</Workbook>
<?php
        exit;
    }

    /**
     * Export data to CSV with UTF-8 BOM
     */
    public static function exportToCsv(string $filename, array $headers, array $rows): void
    {
        $cleanFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename) . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $cleanFilename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM so Excel opens accented & indonesian characters properly
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($out, $headers);
        foreach ($rows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }
}
