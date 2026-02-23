<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;
use App\ItemSale;
use App\ItemPurchase;
use App\Damage;
use DB;

class RequestController extends Controller {

    public function __construct() {
        // This calls your Role middleware and passes 'admin' as the allowed role
        $this->middleware(['auth', 'role:admin']);
    }

    public function index() {
        $pending_sales = ItemSale::with('item', 'user')->where('status', '!=', 'approved')->get();
        $pending_purchases = ItemPurchase::with('item', 'user')->where('status', '!=', 'approved')->get();
        $pending_damages = Damage::with('item', 'user')->where('status', '!=', 'approved')->get();
        $pending_items = Item::with('user', 'itemType', 'itemLocation', 'drawer', 'cabinet')->where('status', '!=', 'approved')->get();

        return view('request.index', compact('pending_sales', 'pending_purchases', 'pending_damages', 'pending_items'));
    }

    public function approveSale($id) {
        $sale = ItemSale::findOrFail($id);
        $item = Item::findOrFail($sale->item_id);

        if ($item->qty < $sale->qty) {
            return back()->with('error', 'Insufficient stock to approve this sale.');
        }

        DB::transaction(function () use ($sale, $item) {
            $item->decrement('qty', $sale->qty);
            $sale->update(['status' => 'approved']);
        });

        return back()->with('success', 'Sale approved and stock updated.');
    }

    public function approvePurchase($id) {
        $purchase = ItemPurchase::findOrFail($id);
        $item = Item::findOrFail($purchase->item_id);

        DB::transaction(function () use ($purchase, $item) {
            $item->increment('qty', $purchase->qty);
            $purchase->update(['status' => 'approved']);
        });

        return back()->with('success', 'Purchase approved and stock added.');
    }

    public function approveDamage($id) {
        $damage = Damage::findOrFail($id);
        $item = Item::findOrFail($damage->item_id);

        if ($item->qty < $damage->qty) {
            return back()->with('error', 'Damage quantity exceeds current stock.');
        }

        DB::transaction(function () use ($damage, $item) {
            $item->decrement('qty', $damage->qty);
            $damage->update(['status' => 'approved']);
        });

        return back()->with('success', 'Damage report approved.');
    }

    public function approveItem($id) {
        $item = Item::findOrFail($id);
        $item->status = 'approved';
        $item->save();
        return back()->with('success', 'Item approved.');
    }

    public function rejectItem($id) {
        $item = Item::findOrFail($id);
        $item->status = 'rejected';
        $item->save();
        return back()->with('success', 'Item rejected.');
    }

    public function declineRequest($id, $type) {
        // Fallback for match if PHP < 8.0, otherwise match is fine
        if ($type == 'sale') {
            $model = ItemSale::findOrFail($id);
        } elseif ($type == 'purchase') {
            $model = ItemPurchase::findOrFail($id);
        } else {
            $model = Damage::findOrFail($id);
        }

        $model->update(['status' => 'rejected']);
        return back()->with('info', 'Request has been rejected.');
    }
}
