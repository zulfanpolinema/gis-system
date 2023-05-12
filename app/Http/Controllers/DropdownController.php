<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FinanceType;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DropdownController extends Controller
{
    public function getRoles()
    {
        $roles = Role::select('id', 'name')
            ->where([
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get();
        $data = [];
        foreach ($roles as $role) {
            $data[] = [
                'id' => $role->id,
                'text' => $role->name,
            ];
        }

        return response()->json(['results' => $data]);
    }

    public function getCategories()
    {
        $categories = Category::select('id', 'name as text')
            ->where([
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get()->toArray();
        return response()->json(['results' => $categories]);
    }

    public function getFarmers()
    {
        $users = User::role('Petani')->select('id', 'name as text')
            ->where([
                ['name', 'like', '%' . request()->input('search', '') . '%']
            ])->get()->toArray();
        return response()->json(['results' => $users]);
    }

    public function getProvinces()
    {
        $provinces = \Indonesia::allProvinces();
        $data = [];
        foreach ($provinces as $province) {
            $data[] = [
                'id'    => $province->id,
                'text'  => ucwords(strtolower($province->name)),
            ];
        }
        return response()->json(['results' => $data]);
    }

    public function getCities(Request $request)
    {
        if ($request->province_id) {
            $cities = \Indonesia::findProvince($request->province_id, ['cities'])->cities;
        } else {
            $cities = \Indonesia::allCities();
        }
        $data = [];
        foreach ($cities as $city) {
            $data[] = [
                'id'    => $city->id,
                'text'  => ucwords(strtolower($city->name)),
            ];
        }
        return response()->json(['results' => $data]);
    }

    public function getSubdistricts(Request $request)
    {
        if ($request->city_id) {
            $districts = \Indonesia::findCity($request->city_id, ['districts'])->districts;
        } else {
            $districts = \Indonesia::allDistricts();
        }
        $data = [];
        foreach ($districts as $district) {
            $data[] = [
                'id'    => $district->id,
                'text'  => ucwords(strtolower($district->name)),
            ];
        }
        return response()->json(['results' => $data]);
    }

    public function getVillages(Request $request)
    {
        if ($request->subdistrict_id) {
            $villages = \Indonesia::findDistrict($request->subdistrict_id, ['villages'])->villages;
        } else {
            $villages = \Indonesia::allVillages();
        }
        $data = [];
        foreach ($villages as $village) {
            $data[] = [
                'id'    => $village->id,
                'text'  => ucwords(strtolower($village->name)),
            ];
        }
        return response()->json(['results' => $data]);
    }
}
