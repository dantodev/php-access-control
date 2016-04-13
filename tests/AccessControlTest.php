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
    $user = new TestUser(["member"]);

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
          "member" => ["view", "test"]
        ],
    ];

    $this->judge = new Judge($config, $user);
  }

  public function test()
  {
    $blog = new TestBlog(["author"], []);
    $comment = new TestComment([], [$blog]);

    $this->assertTrue($this->judge->hasRight("view"));
    $this->assertTrue($this->judge->hasRight("write", $blog));
    $this->assertTrue($this->judge->hasRight("write", $comment));
    $this->assertTrue($this->judge->hasRight("remove", $comment));
    $this->assertTrue($this->judge->hasRight(["view", "test"]));
    $this->assertFalse($this->judge->hasRight("destroy"));
    $this->assertFalse($this->judge->hasRight("destroy", $blog));
    $this->assertFalse($this->judge->hasRight("destroy", $comment));
    $this->assertFalse($this->judge->hasRight("destroy", $comment));
  }

}