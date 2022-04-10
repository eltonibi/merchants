<?php


namespace App\Interfaces;



interface  CardsRepositoryInterface
{
    public function delete(array $number);
    public function get(string $number);
    public function all();
    public function add(array $info);
    public function update(array $info);
}
