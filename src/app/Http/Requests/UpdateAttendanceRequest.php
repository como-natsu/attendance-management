<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateAttendanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'clock_in'  => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i'],
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end'   => ['nullable', 'date_format:H:i'],
            'reason'         => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.date_format'      => '出勤時間は12:34の形式で入力してください',
            'clock_out.date_format'     => '退勤時間は12:34の形式で入力してください',
            'breaks.*.start.date_format'  => '休憩開始は12:34の形式で入力してください',
            'breaks.*.end.date_format'    => '休憩終了は12:34の形式で入力してください',
            'reason.required'           => '備考を記入してください',
        ];
    }

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

                // 各休憩の時間整合チェック
                if (!empty($data['breaks'])) {
                    foreach ($data['breaks'] as $i => $break) {
                        if (!empty($break['start']) && !empty($break['end'])) {
                            $start = Carbon::createFromFormat('H:i', $break['start']);
                            $end   = Carbon::createFromFormat('H:i', $break['end']);
                            if ($start->gt($end)) {
                                $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                            }
                        }

                        if (!empty($break['start']) && !empty($data['clock_out'])) {
                            $clock_out = Carbon::createFromFormat('H:i', $data['clock_out']);
                            $start = Carbon::createFromFormat('H:i', $break['start']);
                            if ($start->gt($clock_out)) {
                                $validator->errors()->add("breaks.$i.start", '休憩時間が不適切な値です');
                            }
                        }

                        if (!empty($break['end']) && !empty($data['clock_out'])) {
                            $clock_out = Carbon::createFromFormat('H:i', $data['clock_out']);
                            $end = Carbon::createFromFormat('H:i', $break['end']);
                            if ($end->gt($clock_out)) {
                                $validator->errors()->add("breaks.$i.end", '休憩時間もしくは退勤時間が不適切な値です');
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // date_formatで拾うので無視
            }
        });
    }
}