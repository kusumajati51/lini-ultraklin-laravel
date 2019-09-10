<?php

namespace App\Http\Controllers\Api\V1;

use Validator;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\V1\Rating;

class RatingController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    function rules()
    {
        $rules = [
            'order_id' => 'unique:ratings',
            'vote' => 'required',
            'comment' => 'required',
        ];

        return $rules;
    }

    function vote($id)
    {

        $validator = Validator::make($this->request->all(), $this->rules());

        if ($validator->fails()) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Invalid data.',
                'data' => $validator->errors()
            ]);
        }

        $order = $this->request->user()->orders()
        ->with(
            'ratings'
        )
        ->find($id);

        if (!is_null($order->ratings)) {
            return response()->json([
                'error_validation' => 1,
                'message' => 'Already rated'
            ]);
        }

        $ratings = new Rating;
        $ratings->order_id = $order->id;
        $ratings->invoice_id = $order->invoice_id;
        $ratings->votes = $this->request->vote;
        $ratings->comments = $this->request->comment;
        $ratings->save();

        return response()->json([
            'success' => 1,
            'message' => 'Rate done.'
        ]);
    }
}
