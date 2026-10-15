<?php

namespace App\Events;

use App\Models\Auction;
use App\Models\AuctionBid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BidPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Auction $auction;
    public AuctionBid $bid;
    public bool $isExtended;
    public bool $isClosed;

    public function __construct(Auction $auction, AuctionBid $bid, bool $isExtended = false, bool $isClosed = false)
    {
        $this->auction = $auction;
        $this->bid = $bid;
        $this->isExtended = $isExtended;
        $this->isClosed = $isClosed;
    }

    /**
     * Channel siaran Pusher.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('auction.' . $this->auction->id),
        ];
    }

    /**
     * Nama event siaran.
     */
    public function broadcastAs(): string
    {
        return 'bid.placed';
    }

    /**
     * Data payload siaran ke frontend client.
     */
    public function broadcastWith(): array
    {
        return [
            'auction_id' => $this->auction->id,
            'bid_amount' => (float) $this->bid->bid_amount,
            'bidder' => [
                'id' => $this->bid->user->id,
                'name' => $this->bid->user->name,
                'campus' => $this->bid->user->campus,
            ],
            'highest_bid' => (float) $this->bid->bid_amount,
            'min_next_bid' => (float) ($this->bid->bid_amount + $this->auction->bid_increment),
            'end_time' => $this->auction->end_time?->toISOString(),
            'is_extended' => $this->isExtended,
            'is_closed' => $this->isClosed,
            'winner_id' => $this->auction->winner_id,
            'timestamp' => now()->toISOString(),
        ];
    }
}
