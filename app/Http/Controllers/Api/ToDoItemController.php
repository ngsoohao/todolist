<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ToDoItemResource;
use App\Models\ToDoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

class ToDoItemController extends BaseController
{
    public function index()
    {
        $toDoItems = ToDoItem::all();
        $groupedItems = [];
        
        foreach($toDoItems as $item){
            $groupedItems[$item->category_name][] = [
                'title'=>$item->title,
                'is_complete'=>$item->is_complete,
            ];
        }
        
        return $this->sendResponse($groupedItems, 'ToDoItems grouped by category name successfully.');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'category_name' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        
        $title = $request->title;
        $category_name = $request->category_name;
        
        $toDoItem = ToDoItem::where('title', $title)->where('category_name', $category_name)->first();
        if($toDoItem){
            return $this->sendError('ToDoItem already exists.');
        }
        else{
            $toDoItem=new ToDoItem();
            $toDoItem->title = $title;
            $toDoItem->category_name = $category_name;
            $toDoItem->save();
            Log::info('User insert from '.$request->ip());
            return $this->sendResponse(new ToDoItemResource($toDoItem), 'ToDoItem created successfully.');
        }


    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string',
            'is_complete' => 'boolean',
            'category_name' => 'string'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        $toDoItem = ToDoItem::find($id);
        
        if(!$toDoItem){
            return $this->sendError('ToDoItem not found.');
        }
        else{

            $title = $request->title;
            $is_complete = $request->is_complete;
            $category_name = $request->category_name;

            if($title){
                $toDoItem->title = $title;
            }
            if($is_complete){
                $toDoItem->is_complete = $is_complete;
            }
            if($category_name){
                $toDoItem->category_name = $category_name;
            }
            $toDoItem->save();
            return $this->sendResponse(new ToDoItemResource($toDoItem), 'ToDoItem updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $toDoItem = ToDoItem::find($id);
        if(!$toDoItem){
            return $this->sendError('ToDoItem not found.');
        }

        $toDoItem->delete();
        return $this->sendResponse([], 'ToDoItem deleted successfully.');
    }
}
