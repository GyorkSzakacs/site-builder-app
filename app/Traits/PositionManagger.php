<?php

namespace App\Traits;

trait PositionManagger
{
    /**
     * Get next position.
     * 
     * @return int $next
     */
    public static function getNextPosition()
    {
        $next = self::max('position') + 1;

        return $next;
    }

     /**
     * Retool positions if the requested has been already occupied.
     * 
     * @param int $position
     * @return void
     */
    public function retoolPositions($position)
    {
        $id = $this->id;
        $parentId = $this->getParentIdColumnValue();

        $parentIdColumnName = self::getParentIdColumnName();

        $occupied = self::where([
                            [$parentIdColumnName, $parentId],
                            ['position', $position]
                        ])->first();
        if($occupied != null && $occupied->id != $id)
        {
            $items = self::where([
                            [$parentIdColumnName, $parentId],
                            ['position', '>=', $position]
                        ])->get();

            foreach($items as $item){
                $newPosition = $item->position + 1;

                $item->update([
                    'position' => $newPosition
                ]);
            }
        }
    }
}