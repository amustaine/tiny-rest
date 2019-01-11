<?php

namespace TinyRest\Tests\Examples\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use TinyRest\Annotations\Property;
use TinyRest\DataProvider\AbstractArrayProvider;
use TinyRest\DataProvider\ProviderInterface;
use TinyRest\Request\AbstractListRequest;

class ArtistListTransferObject extends AbstractListRequest
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Property()
     */
    public $genre;

    /**
     * @var string
     *
     * @Property(name="artist_name")
     */
    public $artistName;

    /**
     * @var integer
     *
     * @Assert\Range(min="1500", max="3000")
     * @Property()
     */
    public $year;

    public function getDataProvider() : ProviderInterface
    {
        $data = [
            [
                'genre'       => 'rock',
                'artist_name' => 'Pink Floyd',
                'year'        => 1985
            ],
            [
                'genre'       => 'rock',
                'artist_name' => 'The Beatles',
                'year'        => 1982
            ],
            [
                'genre'       => 'pop',
                'artist_name' => 'Backstreet Boys',
                'year'        => 1990
            ]
        ];

        return new AbstractArrayProvider(array_filter($data, function ($item) {
            return $this->genre === $item['genre'];
        }));
    }
}
