<?php namespace Dtkahl\AccessControlTests;

use Dtkahl\AccessControl\AccessObject;
use Dtkahl\AccessControl\AccessRole;
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

        $role_member = new AccessRole("member", ["access", "test"], [
            "blog" => ["view", "create"]
        ]);

        $role_author = new AccessRole("author", [], [
            "blog" => ["remove", "edit"],
            "comment" => ["write", "remove"] // TODO extend subscriber + test extended
        ]);
        $role_subscriber = new AccessRole("subscriber", [], [
            "comment" => ["write"]
        ]);
        $role_creator = new AccessRole("creator", [], [
            "comment" => ["edit", "remove"]
        ]);

        $object_blog = new AccessObject("blog", [$role_author, $role_subscriber]);
        $object_comment = new AccessObject("comment", [$role_creator]);

        $this->judge = new Judge([$role_member], [$object_blog, $object_comment], $user);
    }

    public function test()
    {
        $blog = new TestBlog(["author"], []);
        $blog2 = new TestBlog([], []);
        $comment = new TestComment([], [$blog]);
        $comment2 = new TestComment([], [$comment]);
        $comment3 = new TestComment(["creator"], [$comment2]);
        $comment4 = new TestComment([], [$comment3]);

        // TODO test NotAllowedException on checkRight and checkRole

        $this->assertTrue($this->judge->hasRight("access")); // positive global right
        $this->assertTrue($this->judge->hasRight(["access", "test"])); // positive multiple rights
        $this->assertTrue($this->judge->hasRight("create", $blog)); // positive object right
        $this->assertTrue($this->judge->hasRight("edit", $blog)); // positive object related right
        $this->assertTrue($this->judge->hasRight("write", $comment)); // positive related object right
        $this->assertTrue($this->judge->hasRight("remove", $comment)); // positive related object right
        $this->assertTrue($this->judge->hasRight("remove", $comment2)); // positive related object right recursive
        $this->assertTrue($this->judge->hasRight("edit", $comment3)); // some more test
        $this->assertFalse($this->judge->hasRight("destroy")); // negative global right
        $this->assertFalse($this->judge->hasRight(["access", "destroy"])); // negative multiple rights
        $this->assertFalse($this->judge->hasRight("destroy", $blog)); // negative object right
        $this->assertFalse($this->judge->hasRight("destroy", $comment)); // negative related object right
        $this->assertFalse($this->judge->hasRight("edit", $blog2)); // negative whithout role
        $this->assertFalse($this->judge->hasRight("destroy", $comment)); // negative related object right

        $this->assertTrue($this->judge->hasRole("member"));
        $this->assertTrue($this->judge->hasRole("author", $blog));
        $this->assertTrue($this->judge->hasRole("creator", $comment3));
        $this->assertTrue($this->judge->hasRole("creator", $comment4)); // inheritance
        $this->assertFalse($this->judge->hasRole("admin"));
        $this->assertFalse($this->judge->hasRole("creator", $comment));
    }

    public function testCheckRight()
    {
        $this->judge->hasRight("access"); // thats it, alright until exception appears here
    }

    /**
     * @expectedException \Dtkahl\AccessControl\NotAllowedException
     */
    public function testExceptionRight()
    {
        $this->judge->checkRight("destroy");
    }

    public function testCheckRole()
    {
        $this->judge->checkRole("member"); // thats it, alright until exception appears here
    }

    /**
     * @expectedException \Dtkahl\AccessControl\NotAllowedException
     */
    public function testExceptionRole()
    {
        $this->judge->checkRole("master");
    }

}