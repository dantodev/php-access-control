<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\Judge;

class AccessControlTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @var Judge
   */
  private $judge;

  public function setUp()
  {
    $user = new TestUser();

    $config = [
        "objects" => [
            TestBlog::class => [
                "identifier" => "blog",
                "roles" => [
                    "author" => [
                        "rights" => ["write"],
                        "related_rights" => [
                            "comment" => ["write", "remove"]
                        ],
                    ],
                    "subscriber" => [
                        "rights" => [],
                        "related_rights" => [
                            "comment" => ["write"]
                        ]
                    ]
                ]
            ],
            TestComment::class => [
                "identifier" => "comment",
                "roles" => [
                    "creator" => [
                        "rights" => ["edit", "remove"],
                        "related_rights" => []
                    ]
                ]
            ]
        ],
        "global" => [
          "member" => []
        ],
    ];

    $this->judge = new Judge($config, $user);
  }

  public function test()
  {
    var_dump($this->judge->hasRight("write", new TestBlog()));
    var_dump($this->judge->hasRight("write", new TestComment()));
    var_dump($this->judge->hasRight("remove", new TestComment()));
  }

}