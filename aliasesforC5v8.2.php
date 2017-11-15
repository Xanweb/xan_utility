<?php



class Area extends \Concrete\Core\Area\Area{}
abstract class Asset extends \Concrete\Core\Asset\Asset{}
class AssetList extends \Concrete\Core\Asset\AssetList{}
class AttributeSet extends \Concrete\Core\Attribute\Set{}
class AuthenticationType extends \Concrete\Core\Authentication\AuthenticationType{}
class Block extends \Concrete\Core\Block\Block{}
class BlockType extends \Concrete\Core\Block\BlockType\BlockType{}
class BlockTypeList extends \Concrete\Core\Block\BlockType\BlockTypeList{}
class BlockTypeSet extends \Concrete\Core\Block\BlockType\Set{}
abstract class Cache extends \Concrete\Core\Cache\Cache{}
class Request extends \Concrete\Core\Http\Request{}
class CacheLocal extends \Concrete\Core\Cache\CacheLocal{}
class Collection extends \Concrete\Core\Page\Collection\Collection{}
class CollectionAttributeKey extends \Concrete\Core\Attribute\Key\CollectionKey{}
class CollectionVersion extends \Concrete\Core\Page\Collection\Version\Version{}
class ConcreteAuthenticationTypeController extends \Concrete\Authentication\Concrete\Controller{}
class Controller extends \Concrete\Core\Controller\Controller{}
class Conversation extends \Concrete\Core\Conversation\Conversation{}
abstract class ConversationEditor extends \Concrete\Core\Conversation\Editor\Editor{}
class ConversationFlagType extends \Concrete\Core\Conversation\FlagType\FlagType{}
class ConversationMessage extends \Concrete\Core\Conversation\Message\Message{}
abstract class ConversationRatingType extends \Concrete\Core\Conversation\Rating\Type{}
class Cookie extends \Concrete\Core\Cookie\Cookie{}
class Environment extends \Concrete\Core\Foundation\Environment{}
class FacebookAuthenticationTypeController extends \Concrete\Authentication\Facebook\Controller{}
class File extends \Concrete\Core\File\File{}
class FileAttributeKey extends \Concrete\Core\Attribute\Key\FileKey{}
class FileImporter extends \Concrete\Core\File\Importer{}
class FileList extends \Concrete\Core\File\FileList{}
class FilePermissions extends \Concrete\Core\Legacy\FilePermissions{}
class FileSet extends \Concrete\Core\File\Set\Set{}
class GlobalArea extends \Concrete\Core\Area\GlobalArea{}
class Group extends \Concrete\Core\User\Group\Group{}
class GroupList extends \Concrete\Core\User\Group\GroupList{}
class GroupSet extends \Concrete\Core\User\Group\GroupSet{}
class GroupSetList extends \Concrete\Core\User\Group\GroupSetList{}
class GroupTree extends \Concrete\Core\Tree\Type\Group{}
class GroupTreeNode extends \Concrete\Core\Tree\Node\Type\Group{}
abstract class Job extends \Concrete\Core\Job\Job{}
class JobSet extends \Concrete\Core\Job\Set{}
class Loader extends \Concrete\Core\Legacy\Loader{}
class Localization extends \Concrete\Core\Localization\Localization{}
class Marketplace extends \Concrete\Core\Marketplace\Marketplace{}
class Package extends \Concrete\Core\Package\Package{}
class Page extends \Concrete\Core\Page\Page{}
abstract class PageCache extends \Concrete\Core\Cache\Page\PageCache{}
class PageController extends \Concrete\Core\Page\Controller\PageController{}
class PageEditResponse extends \Concrete\Core\Page\EditResponse{}
class PageList extends \Concrete\Core\Page\PageList{}
class PageTemplate extends \Concrete\Core\Page\Template{}
class PageTheme extends \Concrete\Core\Page\Theme\Theme{}
class PageType extends \Concrete\Core\Page\Type\Type{}
class PermissionAccess extends \Concrete\Core\Permission\Access\Access{}
class PermissionKey extends \Concrete\Core\Permission\Key\Key{}
class PermissionKeyCategory extends \Concrete\Core\Permission\Category{}
class Permissions extends \Concrete\Core\Permission\Checker{}
class Queue extends \Concrete\Core\Foundation\Queue\Queue{}
abstract class QueueableJob extends \Concrete\Core\Job\QueueableJob{}
class Redirect extends \Concrete\Core\Routing\Redirect{}
class RedirectResponse extends \Concrete\Core\Routing\RedirectResponse{}
class Response extends \Concrete\Core\Http\Response{}
class Router extends \Concrete\Core\Routing\Router{}
class SinglePage extends \Concrete\Core\Page\Single{}
class Stack extends \Concrete\Core\Page\Stack\Stack{}
class StackList extends \Concrete\Core\Page\Stack\StackList{}
class StartingPointPackage extends \Concrete\Core\Package\StartingPointPackage{}
class TaskPermission extends \Concrete\Core\Legacy\TaskPermission{}
class User extends \Concrete\Core\User\User{}
class UserAttributeKey extends \Concrete\Core\Attribute\Key\UserKey{}
class UserList extends \Concrete\Core\User\UserList{}
class View extends \Concrete\Core\View\View{}
abstract class Workflow extends \Concrete\Core\Workflow\Workflow{}
/**
 * @see  \Illuminate\Container\Container
 * @method mixed make(string  $abstract, array   $parameters) Resolve the given type from the container.
 * @method void bind(string $abstract, Closure|string|null $concrete = null, bool $shared = false) Register a binding with the container.
 * @method void singleton(string $abstract, Closure|string|null $concrete = null) Register a shared binding in the container.
 */
class Core extends \Concrete\Core\Support\Facade\Application{}
/**
 * @see \Symfony\Component\HttpFoundation\Session\Session
 * @method boolean has(string $name)
 * @method mixed get(string $name, mixed $default)
 * @method void set(string $name, mixed $value)
 * @method array all()
 * @method mixed remove(string $name)
 * @method FlashBagInterface getFlashBag()
 */
class Session extends \Concrete\Core\Support\Facade\Session{}
/**
 * @see  \Concrete\Core\Database\DatabaseManager
 * @method \Concrete\Core\Database\Connection\Connection connection(string $name = null) Get a database connection instance.
 */
class Database extends \Concrete\Core\Support\Facade\Database{}
/**
 * @see \Concrete\Core\Database\DatabaseManagerORM
 * @method \Doctrine\ORM\EntityManager entityManager()
 */
class ORM extends \Concrete\Core\Support\Facade\DatabaseORM{}

/**
 * @see \Symfony\Component\EventDispatcher\EventDispatcher
 * @method void addListener(string $eventName, callable $listener, int $priority = 0) Adds an event listener that listens on the specified events.
 * @method \Symfony\Component\EventDispatcher\Event dispatch($eventName, \Symfony\Component\EventDispatcher\Event $event = null)
 */
class Events extends \Concrete\Core\Support\Facade\Events{}
class Express extends \Concrete\Core\Support\Facade\Express{}
/**
 * @see  \Concrete\Core\Routing\Router
 * @method void register($rtPath, $callback, $rtHandle = null, $additionalAttributes = array())
 * @method void registerMultiple(array $routes)
 */
class Route extends \Concrete\Core\Support\Facade\Route{}
/**
 * @see \Concrete\Core\Site\Service
 * @method mixed getSite()
 */
class Site extends \Concrete\Core\Support\Facade\Site{}
/**
 * @see \Concrete\Core\User\UserInfoRepository
 * @method \Concrete\Core\User\UserInfo getByID(int $uID) Returns the UserInfo object for a give userclass s uID.
 * @method \Concrete\Core\User\UserInfo getByName(string $uName) Returns the UserInfo object for a give userclass s username.
 * @method \Concrete\Core\User\UserInfo getByEmail(string $uEmail) Returns the UserInfo object for a give userclass s email address.
 * @method \Concrete\Core\User\UserInfo getByValidationHash(string $uHash, bool $unredeemedHashesOnly)
 */
class UserInfo extends \Concrete\Core\Support\Facade\UserInfo{}
/**
 * @see \Concrete\Core\Filesystem\ElementManager
 * 
 * @method \Concrete\Core\Filesystem\Element get(string $element)
 * @method void register($element, $object)
 * @method void unregister($element)
 */
class Element extends \Concrete\Core\Support\Facade\Element{}
/**
 * @see \Concrete\Core\Logging\Logger
 * @method boolean debug(string $message, array $context) Adds a log record at the DEBUG level.
 * @method boolean alert(string $message, array $context) Adds a log record at the ALERT level.
 * @method boolean warn(string $message, array $context) Adds a log record at the WARNING level.
 * @method boolean info(string $message, array $context) Adds a log record at the INFO level.
 */
class Log extends \Concrete\Core\Support\Facade\Log{}
/**
 * @see \Imagine\Image\AbstractImagine
 * @method \Imagine\Image\ImagineInterface setMetadataReader(Imagine\Image\Metadata\MetadataReaderInterface $metadataReader)
 * @method \Imagine\Image\ImageInterface load(string $string) Loads an image from a binary $string
 * @method \Imagine\Image\ImageInterface open(string $path) Opens an existing image from $path
 * @method \Imagine\Image\ImageInterface read(resource $resource) Loads an image from a resource $resource
 */
class Image extends \Concrete\Core\Support\Facade\Image{}
/**
 * @see  \Concrete\Core\Config\Repository\Repository
 * @method void save(string $key, mixed $cfValue) Set a config item
 * @method mixed get(string $key, mixed $default = null) Get a config item
 * @method void clear(string $cfKey) Delete a config item
 */
class Config extends \Concrete\Core\Support\Facade\Config{}
class URL extends \Concrete\Core\Support\Facade\Url{}    
