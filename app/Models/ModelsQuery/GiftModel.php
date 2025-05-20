<?php

namespace App\Models\ModelsQuery;

use App\Models\Gift;
use App\Models\GiftDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GiftModel extends Model
{
    public function getAllGifts($request)
    {
        $query = Gift::query();
        $query->whereNull('deleted_at')->whereNull('deleted_by');
        if (!empty($request['id'])) {
            $query->where('id', $request['id']);
        }
        if (!empty($request['title'])) {
            $query->where('title', 'like', '%' . $request['id'] . '%');
        }
        $query->with('details');
        $limit = $request['limit'] ?? 10;
        if ($limit == 1) {
            return $query->first();
        }
        if ($limit > 1) {
            return $query->paginate($limit);
        }
    }

    public function createOrUpdate($req)
    {
        try {
            DB::beginTransaction();
            if (!empty($req['id'])) {
                $gift = Gift::whereNull('deleted_at')->find($req['id']);
            }
            if (empty($req['id'])) {
                $gift = new Gift();
                $gift->created_by = auth()->user()->id;
            }
            $gift->updated_at = Carbon::now();
            $gift->updated_by = auth()->user()->id;
            $gift->title = $req['title'] ?? $gift->title;

            $gift->save();
            if (!empty($req['details'])) {
                GiftDetail::whereNull('deleted_at')->where('gift_id', $gift->id)->update([
                    'deleted_at' => now(),
                    'deleted_by' => auth()->user()->id
                ]);

                foreach ($req['details'] as $detail) {
                    $new_detail = new GiftDetail();
                    $new_detail->gift_id = $gift->id;
                    $new_detail->title = $detail['title'];
                    $new_detail->created_by = auth()->user()->id;
                    $new_detail->created_at = Carbon::now();
                    $new_detail->save();
                }
            }

            DB::commit();
            return $gift;
        } catch (\Exception $e) {
            DB::rollBack();
            // throw $e;
            return ['status' => 500, 'message' => $e->getMessage()];
        }
    }
}
