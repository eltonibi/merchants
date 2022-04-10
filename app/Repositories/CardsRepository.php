<?php


namespace App\Repositories;

use App\Interfaces\CardsRepositoryInterface;
use App\Models\Cards;

use Symfony\Component\VarDumper\VarDumper;


class CardsRepository implements CardsRepositoryInterface
{
    /**
     * Get saved card info by card number
     * @param string $number
     * @return false|mixed
     */
    public function get(string $number)
    {
        return Cards::where('number', $number)->get()[0]??false;
    }

    /**
     * Delete card
     * @param array $number
     */
    public function delete(array $number)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Get all cards
     */
    public function all()
    {
        // TODO: Implement all() method.
    }

    /**
     * Add new card
     * @param array $info
     */
    public function add(array $info)
    {
        // TODO: Implement add() method.
    }

    /**
     * Update existing card
     * @param array $info
     */
    public function update(array $info)
    {
        // TODO: Implement update() method.
    }
}
