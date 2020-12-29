<?php


namespace App\Tests\Entity;


use App\Entity\Item;
use App\Entity\Todolist;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

class TodolistEntityTest extends KernelTestCase
{
    /**
     * @return Todolist
     */
    public function getEntity(): Todolist
    {
        return (new Todolist())
            ->setUser(new User())
            ->addItem(new Item())
            ->addItem(new Item());
    }

    public function assertHasErrors(Todolist $todolist, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($todolist);
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

    public function testEntityWithMoreThan10Items()
    {
        $entity = $this->getEntity(); // 2 items
        for($i = 0; $i <= 9; $i++) {
            $entity->addItem(new Item());
        }
        $this->assertHasErrors($entity, 1);
    }

}