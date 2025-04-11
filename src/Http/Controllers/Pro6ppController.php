<?php

namespace Rapidez\Pro6ppPostcode\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class Pro6ppController
{
    public function __invoke(Request $request)
    {
        $request->merge([
            'postcode' => strtoupper(str_replace(' ', '', $request->postcode)),
        ])->validate([
            'postcode' => 'required|string|max:6',
            'housenumber' => 'required',
        ]);

        $cacheKey = 'pro6pp-' . $request->postcode;

        $response = Cache::rememberForever($cacheKey, function () use ($request) {
            return Http::pro6pp()->get('/autocomplete', [
                'nl_sixpp' => $request->postcode
            ])->throw()->json();
        });

        $response['results'] = collect($response['results'])->filter(function ($result) use ($request) {
            return $this->isHouseNumberValid($request->housenumber, $result['streetnumbers']);
        })->values()->all();

        return $response;
    }

    protected function isHouseNumberValid(string $housenumber, string $validRanges /** = '1;11-13;21-27;33;37-49;1 A;1 B;1 C;1 D;1 E' */)
    {
        $houseNumberWithoutAddition = (int) preg_replace('/\D/', '', $housenumber);

        return collect(explode(';', $validRanges))->contains(function ($range) use ($housenumber, $houseNumberWithoutAddition) {
            $range = trim($range);
            if ($range === $housenumber || (int) preg_replace('/\D/', '', $range) === $houseNumberWithoutAddition) {
                return true;
            }

            if (strpos($range, '-') !== false) {
                list($start, $end) = array_map('trim', explode('-', $range));
                return is_numeric($start) && is_numeric($end) && $houseNumberWithoutAddition >= $start && $houseNumberWithoutAddition <= $end;
            }

            return false;
        });
    }
}
