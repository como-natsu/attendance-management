<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateAttendanceRequest extends FormRequest

{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    public function rules()
    {
        return [
            'clock_in'  => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i'],
            'break1_start' => ['nullable', 'date_format:H:i'],
            'break1_end'   => ['nullable', 'date_format:H:i'],
            'break2_start' => ['nullable', 'date_format:H:i'],
            'break2_end'   => ['nullable', 'date_format:H:i'],
            'reason'         => ['required', 'string', 'max:255'],
        ];
    }

public function messages(): array
    {
        return [
            'clock_in.date_format'      => '出勤時間は12:34の形式で入力してください',
            'clock_out.date_format'     => '退勤時間は12:34の形式で入力してください',
            'break1_start.date_format'  => '休憩1開始は12:34の形式で入力してください',
            'break1_end.date_format'    => '休憩1終了は12:34の形式で入力してください',
            'break2_start.date_format'  => '休憩2開始は12:34の形式で入力してください',
            'break2_end.date_format'    => '休憩2終了は12:34の形式で入力してください',
            'reason.required'           => '備考を記入してください',
        ];
    }

    /**
     * Additional validation after default rules.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->all();

            try {
                // 出勤 < 退勤
                if (!empty($data['clock_in']) && !empty($data['clock_out'])) {
                    $clock_in  = Carbon::createFromFormat('H:i', $data['clock_in']);
                    $clock_out = Carbon::createFromFormat('H:i', $data['clock_out']);
                    if ($clock_in->gt($clock_out)) {
                        $validator->errors()->add('clock_in', '出勤時間が不適切な値です');
                    }
                }

                // 休憩1 start < end, start < clock_out, end < clock_out
                if (!empty($data['break1_start'])) {
                    $break1_start = Carbon::createFromFormat('H:i', $data['break1_start']);
                    if (!empty($data['break1_end'])) {
                        $break1_end = Carbon::createFromFormat('H:i', $data['break1_end']);
                        if ($break1_start->gt($break1_end)) {
                            $validator->errors()->add('break1_start', '休憩時間が不適切な値です');
                        }
                    }
                    if (!empty($data['clock_out'])) {
                        $clock_out = Carbon::createFromFormat('H:i', $data['clock_out']);
                        if ($break1_start->gt($clock_out)) {
                            $validator->errors()->add('break1_start', '休憩時間が不適切な値です');
                        }
                    }
                }

                if (!empty($data['break1_end']) && !empty($data['clock_out'])) {
                    $break1_end = Carbon::createFromFormat('H:i', $data['break1_end']);
                    $clock_out  = Carbon::createFromFormat('H:i', $data['clock_out']);
                    if ($break1_end->gt($clock_out)) {
                        $validator->errors()->add('break1_end', '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }

                // 休憩2 start < end, start < clock_out, end < clock_out
                if (!empty($data['break2_start'])) {
                    $break2_start = Carbon::createFromFormat('H:i', $data['break2_start']);
                    if (!empty($data['break2_end'])) {
                        $break2_end = Carbon::createFromFormat('H:i', $data['break2_end']);
                        if ($break2_start->gt($break2_end)) {
                            $validator->errors()->add('break2_start', '休憩時間が不適切な値です');
                        }
                    }
                    if (!empty($data['clock_out'])) {
                        $clock_out = Carbon::createFromFormat('H:i', $data['clock_out']);
                        if ($break2_start->gt($clock_out)) {
                            $validator->errors()->add('break2_start', '休憩時間が不適切な値です');
                        }
                    }
                }

                if (!empty($data['break2_end']) && !empty($data['clock_out'])) {
                    $break2_end = Carbon::createFromFormat('H:i', $data['break2_end']);
                    $clock_out  = Carbon::createFromFormat('H:i', $data['clock_out']);
                    if ($break2_end->gt($clock_out)) {
                        $validator->errors()->add('break2_end', '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            } catch (\Exception $e) {
                // フォーマット不正の場合は rules() の date_format が弾くのでここでは無視
            }
        });
    }
}
