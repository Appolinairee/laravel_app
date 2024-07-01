<?php
namespace App\Http\Traits;

use Exception;
use Illuminate\Http\Response;

trait CrudActions
{
    protected function tryCatchWrapper($callback)
    {
        try {
            return $callback();
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getSingleTrait($model, $id)
    {
        return $this->tryCatchWrapper(function () use ($model, $id) {
            $item = $model::findOrFail($id);
            return response()->json([
                'status' => true,
                'data' => $item
            ]);
        });
    }

    public function getAllTrait($model)
    {
        return $this->tryCatchWrapper(function () use ($model) {
            $items = $model::all();
            return response()->json([
                'status' => true,
                'data' => $items
            ]);
        });
    }

    public function destroyTrait($model, $id)
    {
        return $this->tryCatchWrapper(function () use ($model, $id) {
            $item = $model::findOrFail($id);
            $item->delete();
            return response()->json([
                'status' => true,
                'message' => ucfirst(class_basename($model)) . ' supprimé avec succès.'
            ]);
        });
    }
}