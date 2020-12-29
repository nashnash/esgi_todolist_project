<?php


namespace App\Service;


use App\Entity\Item;
use App\Entity\Todolist;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ToDoListService
{

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var MailService
     */
    private $mailService;

    public function __construct(ValidatorInterface $validator, ObjectManager $objectManager, MailService $mailService)
    {
        $this->validator = $validator;
        $this->objectManager = $objectManager;
        $this->mailService = $mailService;
    }

    /**
     * @param User $user
     * @param Item $item
     * @return bool
     */
    public function add(User $user, Item $item): bool
    {
        if (count($this->validator->validate($item)) === 0) {

            // Check if user have one todolist
            if (is_null($user->getTodolist())) {
                $user->setTodolist(new Todolist());
            }

            if (count($this->validator->validate($user->getTodolist()))) {
                throw new RuntimeException('Unable to add another item in your todolist');
            }

            // TODO: Can be improved
            $item->setCreatedAt(new DateTime());

            $user->getTodolist()->addItem($item);

            if (count($user->getTodolist()->getItems()) === 8) {
                $this->mailService->notifyUserEightItems($user);
            }

            $this->objectManager->persist($item);
            $this->objectManager->persist($user->getTodolist());

            $this->objectManager->persist($user);
            $this->objectManager->flush();

            return true;
        } else {
            throw new RuntimeException('Unable to add an invalid item');
        }
    }

}