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
                        ]
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
            "member" => [
                "rights" => ["access", "test"],
                "related_rights" => [
                  "blog" => ["view"]
                ]
            ]
        ]
    ];

    $this->judge = new Judge($config, $user);
  }

  public function test()
  {
    $blog = new TestBlog(["author"], []);
    $comment = new TestComment([], [$blog]);

    $this->assertTrue($this->judge->hasRight("access")); // positive global right
    $this->assertTrue($this->judge->hasRight(["access", "test"])); // positive multiple rights
    $this->assertTrue($this->judge->hasRight("write", $blog)); // positive object right
    $this->assertTrue($this->judge->hasRight("view", $blog)); // positive object related right
    $this->assertTrue($this->judge->hasRight("write", $comment)); // positive related object right
    $this->assertTrue($this->judge->hasRight("remove", $comment)); // positive related object right
    $this->assertFalse($this->judge->hasRight("destroy")); // negative global right
    $this->assertFalse($this->judge->hasRight(["access", "destroy"])); // negative multiple rights
    $this->assertFalse($this->judge->hasRight("destroy", $blog)); // negative object right
    $this->assertFalse($this->judge->hasRight("destroy", $comment)); // negative related object right
    $this->assertFalse($this->judge->hasRight("destroy", $comment)); // negative related object right

    $this->assertTrue($this->judge->hasRole("member"));
    $this->assertTrue($this->judge->hasRole("author", $blog));
    $this->assertFalse($this->judge->hasRole("admin"));
    $this->assertFalse($this->judge->hasRole("creator", $comment));
  }

}