<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class InvoicePrintSettingController extends Controller
{
    /** Thermal options this page edits; the rest of invoice_customization stays as saved. */
    private const THERMAL_KEYS = [
        'show_company_name', 'show_logo', 'show_shop_address', 'show_shop_email', 'show_shop_phone',
        'show_shop_gst', 'bill_of_supply',
        'show_serial', 'show_unit', 'show_item_mrp', 'show_item_description',
        'show_total_item_count', 'decimal_places', 'show_received', 'show_balance', 'show_customer_outstanding',
        'show_tax_details', 'amount_grouping', 'amount_words_format', 'show_amount_words', 'show_savings',
        'show_remarks', 'enable_terms',
        'default_printer', 'default_printer_label', 'language', 'paper_size', 'extra_end_lines', 'print_copies', 'text_styling',
        'auto_cut', 'open_cash_drawer', 'thermal_theme',
    ];

    public function index()
    {
        $setting = invoice_settings_row();

        $previewOrderId = null;
        if ($setting) {
            $previewOrderId = Order::where('isDeleted', 0)
                ->when($setting->branch_id, fn ($q) => $q->where('branch_id', $setting->branch_id))
                ->latest('id')
                ->value('id');
        }

        return view('settings.invoice-print', [
            'setting' => $setting,
            'regular' => invoice_print($setting),
            'thermal' => invoice_custom($setting),
            'previewOrderId' => $previewOrderId,
        ]);
    }

    public function save(Request $request)
    {
        $setting = invoice_settings_row();
        if (! $setting) {
            return response()->json(['status' => false, 'message' => 'Shop settings not found. Save Shop Settings first.'], 404);
        }

        $regular = $this->clean(default_invoice_print_settings(), invoice_print($setting), (array) $request->input('regular', []));
        $regular['text_size'] = $this->oneOf($regular['text_size'], ['small', 'medium', 'large'], 'medium');
        $regular['page_size'] = $this->oneOf($regular['page_size'], ['a4', 'a5'], 'a4');
        $regular['orientation'] = $this->oneOf($regular['orientation'], ['portrait', 'landscape'], 'portrait');
        $regular['company_name_size'] = $this->oneOf($regular['company_name_size'], ['small', 'medium', 'large', 'xlarge'], 'large');
        $regular['amount_words_format'] = $this->oneOf($regular['amount_words_format'], ['indian', 'international'], 'indian');
        $regular['extra_top_space'] = max(0, min(20, (int) $regular['extra_top_space']));
        $regular['min_item_rows'] = max(0, min(40, (int) $regular['min_item_rows']));
        $regular['signature_text'] = mb_substr(trim((string) $regular['signature_text']), 0, 60) ?: 'Authorized Signatory';
        if (! preg_match('/^#[0-9a-fA-F]{6}$/', (string) $regular['theme_color'])) {
            $regular['theme_color'] = '#ff9f43';
        }

        $thermalDefaults = array_intersect_key(default_invoice_customization(), array_flip(self::THERMAL_KEYS));
        $currentThermal = invoice_custom($setting);
        $thermal = $this->clean($thermalDefaults, $currentThermal, (array) $request->input('thermal', []));
        $thermal['decimal_places'] = max(0, min(4, (int) $thermal['decimal_places']));
        $thermal['print_copies'] = max(1, min(5, (int) $thermal['print_copies']));
        $thermal['extra_end_lines'] = max(0, min(10, (int) $thermal['extra_end_lines']));
        $thermal['thermal_theme'] = max(0, min(5, (int) $thermal['thermal_theme']));
        $thermal['paper_size'] = $this->oneOf($thermal['paper_size'], ['2inch', '3inch'], '3inch');
        $thermal['language'] = $this->oneOf($thermal['language'], ['english', 'hindi'], 'english');
        $thermal['amount_words_format'] = $this->oneOf($thermal['amount_words_format'], ['indian', 'international'], 'indian');
        $thermal['default_printer'] = mb_substr(trim((string) $thermal['default_printer']), 0, 190);
        $thermal['default_printer_label'] = $thermal['default_printer'] === ''
            ? ''
            : (mb_substr(trim((string) $thermal['default_printer_label']), 0, 120) ?: $thermal['default_printer']);
        if ($thermal['thermal_theme'] > 0) {
            $thermal['preferred_template'] = $thermal['thermal_theme'];
        }

        $setting->invoice_print_settings = $regular;
        $setting->invoice_customization = array_replace($currentThermal, $thermal);
        if (in_array($request->input('default_printer'), ['regular', 'thermal'], true)) {
            $setting->invoice_size = $request->input('default_printer') === 'thermal' ? 'small' : 'big';
        }
        if ($request->has('terms')) {
            $setting->terms_condition_eng = trim((string) $request->input('terms')) ?: null;
        }
        $setting->save();

        return response()->json([
            'status' => true,
            'message' => 'Invoice print settings saved',
        ]);
    }

    /**
     * Cast posted values to each default's type; keys not posted keep their saved value.
     */
    private function clean(array $defaults, array $current, array $incoming): array
    {
        $cleaned = [];
        foreach ($defaults as $key => $defaultVal) {
            if (! array_key_exists($key, $incoming)) {
                $cleaned[$key] = $current[$key] ?? $defaultVal;
                continue;
            }
            $val = $incoming[$key];
            if (is_bool($defaultVal)) {
                $cleaned[$key] = filter_var($val, FILTER_VALIDATE_BOOLEAN);
            } elseif (is_int($defaultVal)) {
                $cleaned[$key] = (int) $val;
            } elseif (is_float($defaultVal)) {
                $cleaned[$key] = (float) $val;
            } else {
                $cleaned[$key] = is_string($val) ? trim($val) : (string) $val;
            }
        }

        return $cleaned;
    }

    private function oneOf($value, array $allowed, string $fallback): string
    {
        return in_array($value, $allowed, true) ? $value : $fallback;
    }
}
