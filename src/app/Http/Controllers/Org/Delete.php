<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Org\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Cloth\Unit\OrgIndex;
use App\Http\Controllers\Cloth\Unit\Delete as UnitDelete;
use App\Http\Controllers\Cloth\Stat\Index as StatIndex;
use App\Http\Controllers\Cloth\Stat\Delete as StatDelete;
use App\Http\Controllers\Cloth\Index as ClothIndex;
use App\Http\Controllers\Cloth\Delete as ClothDelete;
use App\Http\Controllers\Cloth\Cat\Index as CatIndex;
use App\Http\Controllers\Cloth\Cat\Delete as CatDelete;
use App\Http\Controllers\House\OrgIndex as OrgHouseIndex;
use App\Http\Controllers\House\OrgDelete as OrgHouseDelete;
use App\Models\Org\OrgUser;
use Illuminate\Support\Facades\DB;
use App\Models\User\User;
use App\Http\Controllers\User\Delete as UserDelete;

class Delete extends Controller
{
    /**
     * org-delete
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        // Find all clothing units associated with organization
        $indexResponse = (new OrgIndex())->index($request);
        if ($indexResponse->status() != 200) {
            return Response($indexResponse->content(), 500);
        }
        $units = json_decode($indexResponse->content(), TRUE);

        // Delete all clothing units
        foreach ($units as $unit) {
            $deleteRequest = Request::create('/?unit_id=' . $unit['id'], 'DELETE');
            $deleteResponse = (new UnitDelete())->delete($deleteRequest);
            if ($deleteResponse->status() != 204) {
                return Response($deleteResponse->content(), 500);
            }
        }

        // Find all statuses associated with organization
        $indexResponse = (new StatIndex())->index($request);
        if ($indexResponse->status() != 200) {
            return Response($indexResponse->content(), 500);
        }
        $stats = json_decode($indexResponse->content(), TRUE);

        // Delete all statuses
        foreach ($stats as $stat) {
            $deleteRequest = Request::create('/?stat_id=' . $stat['id'], 'DELETE');
            $deleteResponse = (new StatDelete())->delete($deleteRequest);
            if ($deleteResponse->status() != 204) {
                return Response($deleteResponse->content(), 500);
            }
        }

        // Find all clothing associated with organization
        $indexResponse = (new ClothIndex())->index($request);
        if ($indexResponse->status() != 200) {
            return Response($indexResponse->content(), 500);
        }
        $cloths = json_decode($indexResponse->content(), TRUE);

        // Delete all clothing
        foreach ($cloths as $cloth) {
            $deleteRequest = Request::create('/?cloth_id=' . $cloth['id'], 'DELETE');
            $deleteResponse = (new ClothDelete())->delete($deleteRequest);
            if ($deleteResponse->status() != 204) {
                return Response($deleteResponse->content(), 500);
            }
        }

        // Find all categories associated with organization
        $indexResponse = (new CatIndex())->index($request);
        if ($indexResponse->status() != 200) {
            return Response($indexResponse->content(), 500);
        }
        $cats = json_decode($indexResponse->content(), TRUE);

        // Delete all categories
        foreach ($cats as $cat) {
            $deleteRequest = Request::create('/?cat_id=' . $cat['id'], 'DELETE');
            $deleteResponse = (new CatDelete())->delete($deleteRequest);
            if ($deleteResponse->status() != 204) {
                return Response($deleteResponse->content(), 500);
            }
        }

        // Find all warehouses associated with organization
        $indexResponse = (new OrgHouseIndex())->index($request);
        if ($indexResponse->status() != 200) {
            return Response($indexResponse->content(), 500);
        }
        $orgHouses = json_decode($indexResponse->content(), TRUE);

        // Delete all organization's warehouses
        foreach ($orgHouses as $orgHouse) {
            $deleteRequest = Request::create('/?house_id=' . $orgHouse['id'], 'DELETE');
            $deleteResponse = (new OrgHouseDelete())->delete($deleteRequest);
            if ($deleteResponse->status() != 204) {
                return Response($deleteResponse->content(), 500);
            }
        }

        // Find users associated only with this organization
        $orgUsers = OrgUser::where('organization', $request->attributes->get('org_id'))->get();
        $users = [];
        foreach ($orgUsers as $orgUser) {
            $user = User::find($orgUser['user']);
            if (count($user->organizations()->get()->toArray()) < 2) {
                $users[] = $user;
            }
        }

        // Delete all organization's users
        foreach ($orgUsers as $orgUser) {
            $orgUser->delete();
        }

        // Delete organization
        Organization::find($request->attributes->get('org_id'))->delete();

        // Delete users associated only with this organization
        foreach ($users as $user) {
            (new UserDelete())->delete($user['id']);
        }

        return Response('organization is deleted', 204);
    }
}
