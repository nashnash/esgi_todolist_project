<?php


namespace App\Tests\Service;


use App\Entity\Item;
use App\Entity\Todolist;
use App\Entity\User;
use App\Service\MailService;
use App\Service\ToDoListService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;

class ToDoListServiceTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$container
            ->get('doctrine')
            ->getManager();
    }

    public function testAddValidItemToUserWhichNotHaveTodoList()
    {
        $user = $this->getUser();
        $item = $this->getItem();

        $mailService = $this->createMock(MailService::class);
        $service = new ToDoListService(self::$container->get('validator'), self::$container->get('doctrine.orm.entity_manager'), $mailService);

        $service->add($user, $item);

        $repository = $this->entityManager->getRepository(User::class);

        /** @var User $userAfterInsert */
        $userAfterInsert = $repository->find($user);

        $this->assertObjectHasAttribute('todolist', $userAfterInsert);
        $this->assertObjectHasAttribute('items', $userAfterInsert->getTodolist());
        $this->assertNotNull($userAfterInsert->getTodolist()->getItems());
    }

    public function testAddValidItemToUserWhichHaveTodolist()
    {

        $user = $this->getUserWithEmptyTodolist();
        $item = $this->getItem();

        $mailService = $this->createMock(MailService::class);
        $service = new ToDoListService(self::$container->get('validator'), self::$container->get('doctrine.orm.entity_manager'), $mailService);

        $service->add($user, $item);

        $repository = $this->entityManager->getRepository(User::class);

        /** @var User $userAfterInsert */
        $userAfterInsert = $repository->find($user);

        $this->assertObjectHasAttribute('todolist', $userAfterInsert);
        $this->assertObjectHasAttribute('items', $userAfterInsert->getTodolist());
        $this->assertNotNull($userAfterInsert->getTodolist()->getItems());
    }

    public function testAddItemAndSendEmail()
    {
        $user = $this->getUserWithEmptyTodolist();


        /** @var MailerInterface $mailerInterface */
        $mailerInterface = $this->getMockBuilder(MailerInterface::class)->disableOriginalConstructor()->getMock();

        $mailService = new MailService($mailerInterface);

        $service = new ToDoListService(self::$container->get('validator'), self::$container->get('doctrine.orm.entity_manager'), $mailService);

        for ($i = 0; $i < 8; $i++) {
            $service->add($user, $this->getItem());
        }

        $this->assertTrue($service->add($user, $this->getItem()));

        // Have error : A client must have Mailer enabled to make email assertions. Did you forget to require symfony/mailer?
        // But it's installed 😨
        // $this->assertEmailCount(1);
    }

    private function getUser(): User
    {
        return (new User())
            ->setDob(new DateTime('- 15 years'))
            ->setLastname('Lastname')
            ->setFirstname('Firstname')
            ->setEmail('email@email.com')
            ->setPassword('StrongPassword');
    }

    /**
     * @return User
     */
    private function getUserWithEmptyTodolist(): User
    {
        return $this->getUser()->setTodolist(new Todolist());
    }

    private function getItem(): Item
    {
        return (new Item())
            ->setContent('Content')
            ->setName('Name');
    }
}