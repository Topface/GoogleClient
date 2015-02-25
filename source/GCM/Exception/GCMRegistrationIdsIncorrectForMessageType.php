<?php
/*
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace alxmsl\Google\GCM\Exception;

/**
 * Except when message have invalid registration ids count for set content type
 * @author alxmsl
 * @date 7/19/14
 */ 
final class GCMRegistrationIdsIncorrectForMessageType extends GCMMessageException {}
 