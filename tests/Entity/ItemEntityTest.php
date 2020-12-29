<?php


namespace App\Tests\Entity;


use App\Entity\Item;
use App\Entity\Todolist;
use DateTime;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * Class ItemEntityTest
 * @package App\Tests\Entity
 */
class ItemEntityTest extends KernelTestCase
{

    public function getEntity(): Item
    {
        $faker = Factory::create();
        return (new Item())
            ->setContent($faker->text(999))
            ->setName($faker->name)
            ->setCreatedAt(new DateTime())
            ->setTodolist(new Todolist());
    }

    public function assertHasErrors(Item $item, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($item);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testInvalidEntityWithEmptyName()
    {
        $this->assertHasErrors($this->getEntity()->setName(''), 1);
    }

    public function testInvalidEntityWithEmptyContent()
    {
        $this->assertHasErrors($this->getEntity()->setContent(''), 1);
    }

    public function testInvalidEntityWithContentExceeding1000Characters()
    {
        $faker = Factory::create();
        $this->assertHasErrors($this->getEntity()->setContent($faker->text(1200)), 1);
    }

}