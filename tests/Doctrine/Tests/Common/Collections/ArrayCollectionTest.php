<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Tests\Common\Collections;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Tests for {@see \Doctrine\Common\Collections\ArrayCollection}
 *
 * @covers \Doctrine\Common\Collections\ArrayCollection
 */
class ArrayCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideDifferentElements
     */
    public function testToArray($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame($elements, $collection->toArray());
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testFirst($elements)
    {
        $collection = new ArrayCollection($elements);
        $this->assertSame(reset($elements), $collection->first());
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testLast($elements)
    {
        $collection = new ArrayCollection($elements);
        $this->assertSame(end($elements), $collection->last());
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testKey($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(key($elements), $collection->key());

        next($elements);
        $collection->next();

        $this->assertSame(key($elements), $collection->key());
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testNext($elements)
    {
        $collection = new ArrayCollection($elements);

        while (true) {
            $collectionNext = $collection->next();
            $arrayNext = next($elements);

            if(!$collectionNext || !$arrayNext) {
                break;
            }

            $this->assertSame($arrayNext,      $collectionNext,        "Returned value of ArrayCollection::next() and next() not match");
            $this->assertSame(key($elements),     $collection->key(),     "Keys not match");
            $this->assertSame(current($elements), $collection->current(), "Current values not match");
        }
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testCurrent($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(current($elements), $collection->current());

        next($elements);
        $collection->next();

        $this->assertSame(current($elements), $collection->current());
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testGetKeys($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(array_keys($elements), $collection->getKeys());
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testGetValues($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(array_values($elements), $collection->getValues());
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testCount($elements)
    {
        $collection = new ArrayCollection($elements);

        $this->assertSame(count($elements), $collection->count());
    }

    /**
     * @dataProvider provideDifferentElements
     */
    public function testIterator($elements)
    {
        $collection = new ArrayCollection($elements);

        $iterations = 0;
        foreach($collection->getIterator() as $key => $item) {
            $this->assertSame($elements[$key], $item, "Item {$key} not match");
            $iterations++;
        }

        $this->assertEquals(count($elements), $iterations, "Number of iterations not match");
    }

    /**
     * @return array
     */
    public function provideDifferentElements()
    {
        return array(
            'indexed'     => array(array(1, 2, 3, 4, 5)),
            'associative' => array(array('A' => 'a', 'B' => 'b', 'C' => 'c')),
            'mixed'       => array(array('A' => 'a', 1, 'B' => 'b', 2, 3)),
        );
    }

    public function testRemove()
    {
        $elements = array(1, 'A' => 'a', 2, 'B' => 'b', 3);
        $collection = new ArrayCollection($elements);

        $this->assertEquals(1, $collection->remove(0));
        unset($elements[0]);

        $this->assertEquals(null, $collection->remove('non-existent'));
        unset($elements['non-existent']);

        $this->assertEquals(2, $collection->remove(1));
        unset($elements[1]);

        $this->assertEquals('a', $collection->remove('A'));
        unset($elements['A']);

        $this->assertEquals($elements, $collection->toArray());
    }

    public function testRemoveElement()
    {
        $elements = array(1, 'A' => 'a', 2, 'B' => 'b', 3, 'A2' => 'a', 'B2' => 'b');
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->removeElement(1));
        unset($elements[0]);

        $this->assertFalse($collection->removeElement('non-existent'));

        $this->assertTrue($collection->removeElement('a'));
        unset($elements['A']);

        $this->assertTrue($collection->removeElement('a'));
        unset($elements['A2']);

        $this->assertEquals($elements, $collection->toArray());
    }

    public function testContainsKey()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'B2' => 'b');
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->containsKey(0),               "Contains index 0");
        $this->assertTrue($collection->containsKey('A'),             "Contains key \"A\"");
        $this->assertTrue($collection->containsKey('null'),          "Contains key \"null\", with value null");
        $this->assertFalse($collection->containsKey('non-existent'), "Doesn't contain key");
    }

    public function testEmpty()
    {
        $collection = new ArrayCollection();
        $this->assertTrue($collection->isEmpty(), "Empty collection");

        $collection->add(1);
        $this->assertFalse($collection->isEmpty(), "Not empty collection");
    }

    public function testContains()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->contains(0),               "Contains Zero");
        $this->assertTrue($collection->contains('a'),             "Contains \"a\"");
        $this->assertTrue($collection->contains(null),            "Contains Null");
        $this->assertFalse($collection->contains('non-existent'), "Doesn't contain an element");
    }

    public function testExists()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertTrue($collection->exists(function($key, $element) {
            return $key == 'A' && $element == 'a';
        }), "Element exists");

        $this->assertFalse($collection->exists(function($key, $element) {
            return $key == 'non-existent' && $element == 'non-existent';
        }), "Element not exists");
    }

    public function testIndexOf()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertSame(array_search(2,              $elements, true), $collection->indexOf(2),              'Index of 2');
        $this->assertSame(array_search(null,           $elements, true), $collection->indexOf(null),           'Index of null');
        $this->assertSame(array_search('non-existent', $elements, true), $collection->indexOf('non-existent'), 'Index of non existent');
    }

    public function testGet()
    {
        $elements = array(1, 'A' => 'a', 2, 'null' => null, 3, 'A2' => 'a', 'zero' => 0);
        $collection = new ArrayCollection($elements);

        $this->assertSame(2,    $collection->get(1),              'Get element by index');
        $this->assertSame('a',  $collection->get('A'),            'Get element by name');
        $this->assertSame(null, $collection->get('non-existent'), 'Get non existent element');
    }

    public function testEnforceOnCreation()
    {
        $enforce = function ($element) {
            if ($element > 99) {
                throw new \InvalidArgumentException('Now an allowed value');
            }
        };

        $this->setExpectedException('InvalidArgumentException');
        new ArrayCollection(array(99, 100), $enforce);
    }

    public function testEnforceOnAdd()
    {
        $enforce = function ($element) {
            if ($element > 99) {
                throw new \InvalidArgumentException('Not an allowed value');
            }
        };

        $collection = new ArrayCollection(array(98, 99), $enforce);

        $this->setExpectedException('InvalidArgumentException');
        $collection->add(100);
    }

    public function testEnforceOnSet()
    {
        $enforce = function ($element) {
            if ($element > 99) {
                throw new \InvalidArgumentException('Not an allowed value');
            }
        };

        $collection = new ArrayCollection(array(98, 99), $enforce);

        $this->setExpectedException('InvalidArgumentException');
        $collection->set(0, 100);
    }

    public function testEnforcedBehaviorInheritedByNewMappedCollections()
    {
        $enforce = function ($element) {
            if ($element > 99) {
                throw new \InvalidArgumentException('Not an allowed value');
            }
        };

        $collection = new ArrayCollection(array(20, 40, 60, 80), $enforce);
        $new = $collection->map(function($element) {
            return $element + 1;
        });

        $this->setExpectedException('InvalidArgumentException');
        $new->set(0, 100);
    }

    public function testEnforcedOnNewValuesInMappedCollections()
    {
        $enforce = function ($element) {
            if ($element > 99) {
                throw new \InvalidArgumentException('Not an allowed value');
            }
        };

        $collection = new ArrayCollection(array(20, 40, 60, 80), $enforce);

        $this->setExpectedException('InvalidArgumentException');
        $collection->map(function($element) {
            return $element * 2;
        });
    }

    public function testEnforcedBehaviorInheritedByNewFilteredCollections()
    {
        $enforce = function ($element) {
            if ($element > 99) {
                throw new \InvalidArgumentException('Not an allowed value');
            }
        };

        $collection = new ArrayCollection(array(20, 40, 60, 80), $enforce);

        $new = $collection->filter(function($element) {
            return $element > 50;
        });

        $this->setExpectedException('InvalidArgumentException');
        $new->add(100);
    }

    public function testEnforcedBehaviorInheritedByFirstNewPartitionedCollections()
    {
        $enforce = function ($element) {
            if ($element > 99) {
                throw new \InvalidArgumentException('Not an allowed value');
            }
        };

        $collection = new ArrayCollection(array(20, 40, 60, 80), $enforce);

        list($new1, $new2) = $collection->partition(function($element) {
            return $element > 50;
        });

        $this->setExpectedException('InvalidArgumentException');
        $new1->add(100);
    }

    public function testEnforcedBehaviorInheritedBySecondNewPartitionedCollections()
    {
        $enforce = function ($element) {
            if ($element > 99) {
                throw new \InvalidArgumentException('Not an allowed value');
            }
        };

        $collection = new ArrayCollection(array(20, 40, 60, 80), $enforce);

        list($new1, $new2) = $collection->partition(function($element) {
            return $element > 50;
        });

        $this->setExpectedException('InvalidArgumentException');
        $new2->add(100);
    }

    public function testEnforcedBehaviorInheritedByNewMatchingCollections()
    {
        $std1 = new \stdClass();
        $std1->foo = 20;

        $std2 = new \stdClass();
        $std2->foo = 40;

        $enforce = function ($element) {
            if ($element->foo > 99) {
                throw new \InvalidArgumentException('Not an allowed value');
            }
        };

        $collection = new ArrayCollection(array($std1, $std2), $enforce);

        $new = $collection->matching(new Criteria(Criteria::expr()->gt("foo", 30)));

        $std3 = new \stdClass();
        $std3->foo = 100;
        $this->setExpectedException('InvalidArgumentException');
        $new->add($std3);
    }
}
