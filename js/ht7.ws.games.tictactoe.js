var ht7 = ht7 || {};
ht7.ws = ht7.ws || {};
ht7.ws.games = ht7.ws.games || {};

ht7.ws.games.tictactoe = {
    callbacks: {
        gotError: function(msg) {
            if (msg.text !== '') {
                ht7.widgets.c5.notification.add('Error', msg.text, 'error');
            }
            ht7.ws.games.tictactoe.layout.overlay.hideLoader(300);
        },
        gotStart: function(msg) {
            window.location.href = msg.url;
        },
        gotTimelimitPassed: function(msgOrg) {
            const $main = $('#ht7-ws-tictactoe');
            let msg, winner;

            let title = '';

            if ($main.data('is-your-turn') && msgOrg === undefined) {
                msg = ht7.ws.games.tictactoe.client.helpers.getBaseMsg('lost');
                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));

                title = $main.data('title-finish-lost');
                winner = $main.find('.players li:last-child .user-id').html();
            } else {
                title = $main.data('title-finish-won');
                winner = $main.data('user-id');
            }

            ht7.ws.games.tictactoe.layout.showFinish(winner, title, 1);
        },
        gotTurn: function(msg) {
            const $main = $('#ht7-ws-tictactoe');

            if (parseInt(msg.player) !== parseInt($main.data('player-no'))) {
                ht7.widgets.simple.timer.start();
            }
            ht7.ws.games.tictactoe.layout.overlay.hideLoader(300);

            if (ht7.ws.games.tictactoe.logic.makeTurn(msg)) {
                let $el, $el2;

                if (ht7.ws.games.tictactoe.layout.helpers.isYourTurn()) {
                    $main.find('.game').addClass('your-turn');
                    $el = $('#ht7-tictactoe-players .players li:first-child');
                    $el2 = $('#ht7-tictactoe-players .players li:last-child');
                } else {
                    $main.find('.game').removeClass('your-turn');
                    $el = $('#ht7-tictactoe-players .players li:last-child');
                    $el2 = $('#ht7-tictactoe-players .players li:first-child');
                }
                $el.addClass('your-turn');
                $el2.removeClass('your-turn');
            }
        }
    },
    client: {
        helpers: {
            getBaseMsg: function(action = '') {
                const $gameContainer = $('#ht7-ws-tictactoe');

                return {
                    action: action,
                    appId: $gameContainer.data('identifier'),
                    icon: $gameContainer.data('user-avatar'),
                    datetime: (new Date()).toString(),
                    hash: $gameContainer.data('game-hash'),
                    iconColor: $gameContainer.data('user-color'),
                    isRoom: ['', undefined].indexOf($gameContainer.data('finish-href')) > -1 ? 1 : 0,
                    player: parseInt($gameContainer.data('player-no'), 10),
                    releaser: 'user',
                    text: '',
                    token: $gameContainer.data('token'),
                    userId: $gameContainer.data('user-id'),
                    userName: $gameContainer.data('user-name')
                };
            }
        },
        init: function() {
            const url = ht7.ws.games.tictactoe.layout.helpers.getMainContainer()
                    .data('ws-url');

            ht7.ws.games.tictactoe.variables.connector = ht7.ws.clients.connector(url, this);
        },
        onClose: function(e) {
//            ht7.ws.chat.simple.widgets.chatOverlay.show();
        },
        onError: function(e) {
//            ht7.ws.chat.simple.widgets.chatOverlay.show();
        },
        onMessage: function(e) {
            if (e.data !== undefined) {
                const msg = JSON.parse(e.data);

                if (msg.action === 'hello') {
                } else if (msg.action === 'game_layout') {
                    if (msg.item === 'show-borders' && msg.value > 0) {
                        $('#ht7-tictactoe-playground .game').removeClass('layout-open');
                    }
                } else if (msg.action === 'lost') {
                    ht7.ws.games.tictactoe.callbacks.gotTimelimitPassed(msg);
                } else if (msg.action === 'turn') {
                    ht7.ws.games.tictactoe.callbacks.gotTurn(msg);
                } else if (msg.action === 'welcome') {
                    ht7.ws.games.tictactoe.layout.helpers.updatePlayerCount();
                    ht7.ws.games.tictactoe.layout.helpers.updatePlayerPanel(msg);
                    const count = ht7.ws.games.tictactoe.layout.helpers.getPlayerCount();

                    if (count === 1) {
                        //
                    } else if (count === 2) {
                        ht7.ws.games.tictactoe.client.onWelcome(msg);
                        ht7.ws.games.tictactoe.layout.overlay.start();

                        let $el;

                        if (ht7.ws.games.tictactoe.layout.helpers.isYourTurn()) {
                            $el = $('#ht7-tictactoe-players .players li:first-child');
                        } else {
                            $el = $('#ht7-tictactoe-players .players li:last-child');
                        }
                        $el.addClass('your-turn');
                    }
                } else if (msg.action === 'error') {
                    // @todo: doesn't work
                    ht7.ws.games.tictactoe.callbacks.gotError(msg);
                } else if (msg.action === 'goodby') {
                    // @todo: doesn't work
                    ht7.ws.chat.simple.output.update(msg);
                }
            }
        },
        onOpen: function(e) {
            const msg = ht7.ws.games.tictactoe.client.helpers.getBaseMsg('hello');

            ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
        },
        onTurn: function($target) {
            let msg = ht7.ws.games.tictactoe.client.helpers.getBaseMsg('turn');

            msg.move = ht7.ws.games.tictactoe.layout.helpers.getCellOrCoor($target).join(',');
//            msg.who = parseInt(ht7.ws.games.tictactoe.layout.helpers.getMainContainer().data('player-no'), 10);

            ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
        },
        onWelcome: function() {
            const msg = ht7.ws.games.tictactoe.client.helpers.getBaseMsg('welcome');

            ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
        }
    },
    init: function() {
        this.layout.init();

        if (!this.layout.helpers.getMainContainer().data('is-ki')) {
            this.layout.overlay.init();
            this.client.init();
        }
    },
    layout: {
        addEventListeners: function() {
            $('table.game td').on('click', ht7.ws.games.tictactoe.layout.callbacks.onClick);
        },
        callbacks: {
            onClick: function(e) {
                if (ht7.ws.games.tictactoe.layout.helpers.isYourTurn()) {
                    if (ht7.ws.games.tictactoe.layout.helpers.getMainContainer().data('is-ki')) {
                        ht7.ws.games.tictactoe.layout.update($(e.target), true);
                        ht7.ws.games.tictactoe.logic.makeTurnKi($(e.target));
                    } else {
                        if (ht7.widgets.simple !== undefined) {
                            ht7.widgets.simple.timer.stop();
                        }
                        ht7.ws.games.tictactoe.layout.overlay.showLoader();
                        ht7.ws.games.tictactoe.client.onTurn($(e.target));
                    }
                }
            }
        },
        helpers: {
            /**
             * Clone the action buttons from the dialog and append them to the
             * <code>$parent</code>.
             * @param {JQuery} $parent
             * @returns {void}
             */
            addActionButtons: function($parent) {
                const $btns = $('.ui-dialog .action-bar').clone(true);
                $btns.css('text-align', 'center').css('margin-top', '30px');
                $parent.append($btns);
            },
            getCellOrCoor: function(data) {
                const $fields = $('table.game td');
                const coor = typeof data === 'string' ? data.split(',') : [];
                let col = 0, row = 0, ind = 0;

                if (!(typeof data === 'string')) {
                    data = data.get(0).tagName === 'I' ? data.parent() : data;
                }

                for (const i of $fields) {
                    if (typeof data === 'string') {
                        if (col + '' === coor[1] && row + '' === coor[0]) {
                            return $(i);
                        }
                    } else if (i === data.get(0)) {
                        return [row, col];
                    }

                    ind++;

                    if (ind > 2) {
                        ind = 0;
                        row++;
                    }
                    col = ind;
                }
            },
            getEmptyFields: function() {
                return $('table.game td:not(.occupied)');
            },
            /**
             * Get an array of the positions.
             * The array will be 2D with the related player number or undefined as values.
             * @returns {Array}
             */
            getFields: function() {
                const fields = $('table.game td');
                const templateId1 = ht7.ws.games.tictactoe.layout.helpers.getTemplateId(true);
                const template1 = document.getElementById(templateId1).content.cloneNode(true);
                const class1 = template1.querySelector('i').className;
                const templateId2 = ht7.ws.games.tictactoe.layout.helpers.getTemplateId(false);
                const template2 = document.getElementById(templateId2).content.cloneNode(true);
                const class2 = template1.querySelector('i').className;
                const arr = [];
                let arrTmp = [];

                for (const i of fields) {
                    if (i.className === '') {
                        arrTmp.push(undefined);
                    } else {
                        if (i.querySelector('i').className === class1) {
                            arrTmp.push(1);
                        } else {
                            arrTmp.push(2);
                        }
                    }

                    if (arrTmp.length > 2) {
                        arr.push(arrTmp);
                        arrTmp = [];
                    }
                }

                return arr;
            },
            getMainContainer: function() {
                return $('#ht7-ws-tictactoe');
            },
            getPlayerCount: function() {
                return parseInt('' + ht7.ws.games.tictactoe.layout.helpers.getMainContainer()
                        .data('player-count'), 10);
            },
            getTemplateId: function(isMe) {
                const $main = ht7.ws.games.tictactoe.layout.helpers.getMainContainer();
                const playerNo = $main.data('player-no');
                return 'ht7-tictactoe-player-' + (isMe ? playerNo : (playerNo === 1 ? 2 : 1));
            },
            isYourTurn: function() {
                return parseInt(ht7.ws.games.tictactoe.layout.helpers.getMainContainer()
                        .data('is-your-turn')) === 1;
            },
            setYourTurn: function(isMe) {
                const val = isMe ? '1' : '0';

                ht7.ws.games.tictactoe.layout.helpers.getMainContainer()
                        .data('is-your-turn', val);

                if (isMe) {
                    $('#ht7-tictactoe-players .players ul li ');
                } else {

                }
            },
            updatePlayerCount: function() {
                const countNew = ht7.ws.games.tictactoe.layout.helpers.getPlayerCount() + 1;

                if (countNew < 3) {
                    ht7.ws.games.tictactoe.layout.helpers.getMainContainer()
                            .data('player-count', countNew);
                    ht7.ws.games.tictactoe.layout.overlay.update(countNew);
                }
            },
            updatePlayerPanel: function(msg) {
                if ($('#ht7-tictactoe-players .players .well li[data-user-id="' + msg.userId + '"]').length > 0) {
                    return;
                } else if ($('#ht7-tictactoe-players .players .well li').length >= 2) {
                    return;
                }

                const $item = $(document.getElementById('settings-player-item').content.cloneNode(true));
                $item.data('user-id', msg.userId);
                $item.attr('data-user-id', msg.userId);
                $item.find('.username').html(msg.userName);
                $item.find('.user-id').html(msg.userId);
                let $icon;

                if (msg.player === 1) {
                    $icon = $('<i class="fa fa-times">');
                    $item.addClass('your-turn');
                } else if (msg.player === 2) {
                    $icon = $('<i class="fa fa-circle">');
                }

                if ($icon !== undefined) {
                    $item.find('.game-icon').html($icon);
                }

                $('#ht7-tictactoe-players .players .well').append($item);
                $('#ht7-tictactoe-players .players li:last-child')
                        .addClass('user-id-' + msg.userId);
            }
        },
        init: function() {
            this.addEventListeners();
        },
        overlay: {
            get: function() {
                return $('#ht7-ws-tictactoe-overlay');
            },
            hide: function(duration = 0) {
                ht7.ws.games.tictactoe.layout.overlay.get()
                        .hide(duration);
            },
            hideLoader: function(duration = 0) {
                ht7.ws.games.tictactoe.layout.overlay.get()
                        .fadeOut(duration);
            },
            init: function() {
                this.show();
            },
            show: function() {
                ht7.ws.games.tictactoe.layout.overlay.get()
                        .show();
            },
            showLoader: function() {
                ht7.ws.games.tictactoe.layout.overlay.get().find('.loader-container').show();
                ht7.ws.games.tictactoe.layout.overlay.get()
                        .show();
            },
            start: function() {
                ht7.ws.games.tictactoe.layout.overlay.get().fadeOut();
                ht7.ws.games.tictactoe.layout.overlay.get().find('.text-container').fadeOut();
            },
            update: function(count) {
                ht7.ws.games.tictactoe.layout.overlay.get().find('.count')
                        .html(count);
            }
        },
        showFinish: function(winner, title, isTimelimit = 0) {
            ht7.widgets.bodyoverlay.show();
            const $main = ht7.ws.games.tictactoe.layout.helpers.getMainContainer();
            const hash = $main.data('game-hash');

            $.post($main.data('finish-href') + '/' + hash + '/' + winner + '/' + isTimelimit, {
                fields: ht7.ws.games.tictactoe.layout.helpers.getFields()
            }, function(data) {
                $.fn.dialog.open({
                    close: $.fn.dialog.closeTop(),
                    element: $('<div>' + data + '</div>'),
                    height: 500,
                    title: title,
                    width: 750
                });
            }).done(function() {
                ht7.ws.games.tictactoe.layout.helpers.addActionButtons($main.parent());
            }).fail(function(data) {
                //
            }).always(function() {

                ht7.widgets.bodyoverlay.hide();
            });
        },
        update: function($el, isMe) {
            const templateId = ht7.ws.games.tictactoe.layout.helpers.getTemplateId(isMe);
            const template = document.getElementById(templateId);
            const clone = template.content.cloneNode(true);

            if (typeof $el === 'string') {
                $el = ht7.ws.games.tictactoe.layout.helpers.getCellOrCoor($el);
            }

            $el.append(clone);
            $el.addClass('occupied');

            ht7.ws.games.tictactoe.layout.helpers.setYourTurn(!isMe);
        }
    },
    lobby: {
        addEventListeners: function() {
            ht7.ws.games.tictactoe.lobby.helpers.getMainContainer()
                    .find('.game-action')
                    .on('click', function() {
                        ht7.widgets.bodyoverlay.show();
                    });
        },
        callbacks: {
//            gotGame: function(msg) {
//
//            }
        },
        client: {
            init: function() {
                const url = ht7.ws.games.tictactoe.lobby.helpers.getMainContainer()
                        .data('ws-url');

                ht7.ws.games.tictactoe.variables.connector = ht7.ws.clients.connector(url, this);

                setTimeout(function() {
                    const $main = ht7.ws.games.tictactoe.lobby.helpers.getMainContainer();
                    // Register this connection.
                    const msg = {
                        action: 'lobby_add',
                        appId: $main.data('identifier'),
                        datetime: (new Date()).toString(),
                        releaser: 'user',
                        text: '',
                        token: $main.data('token'),
                        userId: $main.data('user-id'),
                        userName: $main.data('user-name')
                    };

                    ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
                }, 1000);
            },
            onClose: function(e) {
//                ht7.ws.chat.simple.widgets.chatOverlay.show();
            },
            onError: function(e) {
//                ht7.ws.chat.simple.widgets.chatOverlay.show();
            },
            onMessage: function(e) {
                if (e.data !== undefined) {
                    const msg = JSON.parse(e.data);

                    if (msg.action === 'hello') {
                        //
                    } else if (msg.action === 'welcome') {
//                        ht7.ws.chat.simple.members.update(msg);
                    } else if (msg.action === 'lobby_game_add') {
                        ht7.ws.games.tictactoe.lobby.helpers.addGameOpen(msg);
//                        ht7.ws.games.tictactoe.lobby.callbacks.gotGame(msg);
                    } else if (msg.action === 'lobby_game_remove') {
                        ht7.ws.games.tictactoe.lobby.helpers.removeGameOpen(msg);
                    }
                }
            },
            onOpen: function(e) {
                const $main = ht7.ws.games.tictactoe.lobby.helpers.getMainContainer();
                // Say hello
                const msg = {
                    action: 'hello',
                    appId: $main.data('identifier'),
                    datetime: (new Date()).toString(),
                    hash: '',
//                    hash: $main.data('game-hash'),
                    isRoom: 1,
                    releaser: 'user',
                    text: '',
                    token: $main.data('token'),
                    userId: $main.data('user-id'),
                    userName: $main.data('user-name')
                };

                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
            }
        },
        helpers: {
            addGameOpen: function(msg) {
                const $main = ht7.ws.games.tictactoe.lobby.helpers.getMainContainer();
                const $list = $main.find('.game-list.games-open');

                if ($list.find('li[data-hash="' + msg.hash + '"]').length > 0) {
                    return;
                }

                const $newGame = $(document.getElementById('ht7-game-open-row').content.cloneNode(true));
                $newGame.find('li').data('hash', msg.hash).attr('data-hash', msg.hash);
                $newGame.find('a').attr('href', $newGame.find('a').attr('href') + msg.hash);
                $newGame.find('.title').html($newGame.find('.title').html() + msg.userName);

                $list.append($newGame);
                $list.find('.empty-list').hide();
            },
            getMainContainer: function() {
                return $('#ht7-game-container');
            },
            removeGameOpen: function(msg) {
                const $main = ht7.ws.games.tictactoe.lobby.helpers.getMainContainer();
                const $list = $main.find('.game-list.games-open');
                const $item = $list.find('li[data-hash="' + msg.hash + '"]');

                if ($item.length > 0) {
                    $item.remove();
                }
                if ($list.find('li').length === 1) {
                    $list.find('.empty-list').show();
                }
            }
        },
        init: function() {
            this.addEventListeners();
            this.client.init();
        }
    },
    logic: {
        checkDiagonal: function(f, n) {
            if (f[0][0] === n && f[1][1] === n && f[2][2] === n) {
                return true;
            } else if (f[0][2] === n && f[1][1] === n && f[2][0] === n) {
                return true;
            }

            return false;
        },
        checkHorizontal: function(f, n) {
            const max = 3;

            for (let i = 0; i < max; i++) {
                if (f[i][0] === n && f[i][1] === n && f[i][2] === n) {
                    return true;
                }
            }

            return false;
        },
        checkVertical: function(f, n) {
            const max = 3;

            for (let i = 0; i < max; i++) {
                if (f[0][i] === n && f[1][i] === n && f[2][i] === n) {
                    return true;
                }
            }

            return false;
        },
        checkWinner: function() {
            const $main = ht7.ws.games.tictactoe.layout.helpers.getMainContainer();

            const $occupied = $main.find('table.game td.occupied');

            if ($occupied.length < 5) {
                return -1;
            }

            const fields = ht7.ws.games.tictactoe.layout.helpers.getFields();

            if (ht7.ws.games.tictactoe.logic.checkVertical(fields, 1) ||
                    ht7.ws.games.tictactoe.logic.checkHorizontal(fields, 1) ||
                    ht7.ws.games.tictactoe.logic.checkDiagonal(fields, 1)) {
                return 1;
            } else if (ht7.ws.games.tictactoe.logic.checkVertical(fields, 2) ||
                    ht7.ws.games.tictactoe.logic.checkHorizontal(fields, 2) ||
                    ht7.ws.games.tictactoe.logic.checkDiagonal(fields, 2)) {
                return 2;
            }

            return -1;
        },
        makeTurn: function(msg) {
            const $main = ht7.ws.games.tictactoe.layout.helpers.getMainContainer();
            let title;

            ht7.ws.games.tictactoe.layout.update(
                    msg.move,
                    msg.player === parseInt($main.data('player-no'), 10)
                    );

            if (msg.winner === -1) {
                return true;
            } else if (msg.winner > 0) {
                if (msg.winner === parseInt($main.data('user-id'), 10)) {
                    // Current player has won the game.
                    title = $main.data('title-finish-won');
                } else {
                    // Current player has lost the game.
                    title = $main.data('title-finish-lost');
                }
            } else if (msg.winner === 0) {
                // Draw.
                title = $main.data('title-finish-draw');
            }

            ht7.ws.games.tictactoe.layout.showFinish(msg.winner, title);

            return false;
        },
        makeTurnKi: function($el) {
            const $main = ht7.ws.games.tictactoe.layout.helpers.getMainContainer();

            const winner = ht7.ws.games.tictactoe.logic.checkWinner();
            if (winner === 1) {
                // You have won the game.
                const title = $main.data('title-finish-won');
                ht7.ws.games.tictactoe.layout.showFinish(winner, title);
            } else if (winner === 2) {
                // You have lost the game.
                const title = $main.data('title-finish-lost');
                ht7.ws.games.tictactoe.layout.showFinish(winner, title);
            } else {
                const empties = ht7.ws.games.tictactoe.layout.helpers.getEmptyFields();

                if (empties.length === 0) {
                    // Game is finished without a winner.
                    const title = $main.data('title-finish-draw');
                    ht7.ws.games.tictactoe.layout.showFinish(-1, title);
                } else {
                    const newItem = empties.get(Math.floor(Math.random() * empties.length));
                    ht7.ws.games.tictactoe.layout.update($(newItem), false);
                    const winnerNew = ht7.ws.games.tictactoe.logic.checkWinner();

                    if (winnerNew === 1) {
                        // You have won the game.
                        const title = $main.data('title-finish-won');
                        ht7.ws.games.tictactoe.layout.showFinish(winnerNew, title);
                    } else if (winnerNew === 2) {
                        // You have lost the game.
                        const title = $main.data('title-finish-lost');
                        ht7.ws.games.tictactoe.layout.showFinish(winnerNew, title);
                    } else if (empties.length === 1) {
                        // Game is finished without a winner.
                        const title = $main.data('title-finish-draw');
                        ht7.ws.games.tictactoe.layout.showFinish(-1, title);
                    }
                }
            }
        }
    },
    room: {
        addEventListeners: function() {
            const $main = $('#ht7-game-container');

            $('#ht7-game-abort').on('click', this.callbacks.onAbort);
            $('#ht7-game-launch').on('click', this.callbacks.onLaunch);
            $('#ht7-game-leave').on('click', this.callbacks.onLeave);
            $main.find('.settings-general input').on('change', this.callbacks.onSettings);
        },
        callbacks: {
            gotSettings: function(msg) {
                const $main = $('#ht7-game-container');
                const $el = $main.find('input[name="' + msg.item + '"]');

                if ($el.length === 0) {
                    return;
                }

                if ($el.attr('type') === 'checkbox') {
                    $el.prop('checked', (msg.value === 1 ? true : false));
                } else {
                    $el.val(msg.value);
                }
            },
            gotWelcome: function(msg) {
                ht7.ws.games.tictactoe.room.members.add(msg);
                ht7.ws.games.tictactoe.variables.counter++;

                if (ht7.ws.games.tictactoe.variables.counter === 2) {
                    $('#ht7-game-launch').show(500);
                }

                const $main = $('#ht7-game-container');
                // Add this game to the open game list of the lobby.
                const welcome = {
                    action: 'welcome',
                    appId: $main.data('identifier'),
                    datetime: (new Date()).toString(),
                    hash: $main.data('game-hash'),
                    player: $main.data('player-no'),
                    releaser: 'user',
                    text: '',
                    token: $main.data('token'),
                    userId: $main.data('user-id'),
                    userName: $main.data('user-name')
                };

                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(welcome));
            },
            onAbort: function(e) {
                const $main = $('#ht7-game-container');
                // Let the lobby clean up this game.
                const msg = {
                    action: 'lobby_game_remove',
                    appId: $main.data('identifier'),
                    datetime: (new Date()).toString(),
                    hash: $main.data('game-hash'),
                    isRoom: 1,
                    releaser: 'user',
                    text: '',
                    token: $main.data('token'),
                    userId: $main.data('user-id'),
                    userName: $main.data('user-name')
                };

                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));

                // Let the resting player 2 redirect to the lobby.
                const msgDelete = {
                    action: 'delete',
                    appId: $main.data('identifier'),
                    datetime: (new Date()).toString(),
                    hash: $main.data('game-hash'),
                    isRoom: 1,
                    releaser: 'user',
                    text: '',
                    token: $main.data('token'),
                    userId: $main.data('user-id'),
                    userName: $main.data('user-name')
                };

                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msgDelete));
            },
            onLaunch: function(e) {
                ht7.ws.games.tictactoe.room.callbacks.onStart(e);
            },
            onLeave: function(e) {
                const $main = $('#ht7-game-container');
                // Readd this game to the open game list of the lobby.
                const msg = {
                    action: 'leave',
                    appId: $main.data('identifier'),
                    datetime: (new Date()).toString(),
                    hash: $main.data('game-hash'),
                    player: $main.data('player-no'),
                    releaser: 'user',
                    text: '',
                    token: $main.data('token'),
                    userId: $main.data('user-id'),
                    userName: $main.data('user-name')
                };

                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
            },
            onSettings: function(e) {
                const $main = $('#ht7-game-container');
                const $el = $(e.target);
                const value = $el.attr('type') === 'checkbox' ? ($el.is(':checked') ? 1 : 0) : $el.val();
                // Add this game to the open game list of the lobby.
                const msg = {
                    action: 'game_settings',
                    appId: $main.data('identifier'),
                    datetime: (new Date()).toString(),
                    hash: $main.data('game-hash'),
                    item: $el.attr('name'),
                    releaser: 'user',
                    text: '',
                    token: $main.data('token'),
                    userId: $main.data('user-id'),
                    userName: $main.data('user-name'),
                    value: value
                };

                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
            },
            onStart: function() {
                const $main = $('#ht7-game-container');

                const msg = {
                    action: 'start',
                    appId: $main.data('identifier'),
                    datetime: (new Date()).toString(),
                    hash: $main.data('game-hash'),
                    isRoom: 1,
                    player: 1,
                    releaser: 'user',
                    token: $main.data('token'),
                    userId: $main.data('user-id'),
                    userName: $main.data('user-name'),
                    url: $('#ht7-game-launch').attr('href')
                };

                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
            },
            updateSettings: function() {
                const $main = $('#ht7-game-container');
                const $el = $main.find('[name="show-borders"]');
                const value = $el.is(':checked') ? 1 : 0;

                if (value === 1) {
                    const msg = {
                        action: 'game_settings',
                        appId: $main.data('identifier'),
                        datetime: (new Date()).toString(),
                        hash: $main.data('game-hash'),
                        item: $el.attr('name'),
                        releaser: 'user',
                        text: '',
                        token: $main.data('token'),
                        userId: $main.data('user-id'),
                        userName: $main.data('user-name'),
                        value: value
                    };

                    ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
                }
            }
        },
        client: {
            init: function() {
                const url = $('#ht7-game-container').data('ws-url');

                ht7.ws.games.tictactoe.variables.connector = ht7.ws.clients.connector(url, this);
            },
            onClose: function(e) {
//                ht7.ws.chat.simple.widgets.chatOverlay.show();
            },
            onError: function(e) {
//                ht7.ws.chat.simple.widgets.chatOverlay.show();
            },
            onMessage: function(e) {
                if (e.data !== undefined) {
                    const msg = JSON.parse(e.data);

                    if (msg.action === 'hello') {
                        //
                    } else if (msg.action === 'delete') {
                        setTimeout(function() {
                            window.location.href = window.location.origin + '/tictactoe/lobby/deleted';
                        }, 1000);
                    } else if (msg.action === 'leave') {
                        ht7.ws.games.tictactoe.variables.counter = 1;
                        $('#ht7-game-container .settings-personal .players li:last-child')
                                .remove();
                        $('#ht7-game-launch').hide();
                    } else if (msg.action === 'game_settings') {
                        ht7.ws.games.tictactoe.room.callbacks.gotSettings(msg);
                    } else if (msg.action === 'start') {
                        ht7.ws.games.tictactoe.callbacks.gotStart(msg);
                    } else if (msg.action === 'welcome') {
                        if (ht7.ws.games.tictactoe.variables.counter < 2) {
                            ht7.ws.games.tictactoe.room.callbacks.gotWelcome(msg);
                            ht7.ws.games.tictactoe.room.callbacks.updateSettings();
                        }
                    }
                }
            },
            onOpen: function(e) {
                const $main = $('#ht7-game-container');
                // Say hello
                const msg = {
                    action: 'hello',
                    appId: $main.data('identifier'),
                    datetime: (new Date()).toString(),
                    hash: $main.data('game-hash'),
                    isRoom: 1,
                    player: parseInt($main.data('player-no'), 10),
                    releaser: 'user',
                    text: '',
                    token: $main.data('token'),
                    userId: $main.data('user-id'),
                    userName: $main.data('user-name')
                };

                ht7.ws.games.tictactoe.variables.connector.send(JSON.stringify(msg));
            }
        },
        init: function() {
            this.addEventListeners();
            this.client.init();
        },
        members: {
            add: function(msg) {
                const $item = $($('#settings-player-item').get(0).content.cloneNode(true));

                $item.find('.username').html(msg.userName);
                $item.find('.game-icon').html('<i class="' + (msg.player === 1 ? 'fa fa-times' : 'fa fa-circle') + '"></i>');

                $('#ht7-game-container .settings-personal .players').append($item);
            },
            remove: function(msg) {

            }
        }
    },
    variables: {
        connector: undefined,
        counter: 0
    },
    watch: {
        addFields: function() {
            const fields = $('#ht7-ws-tictactoe').data('fields');
            const p1 = 'fa fa-times';
            const p2 = 'fa fa-circle';
            let i = 0;

            const $rows = $('#ht7-ws-tictactoe .game tbody tr');

            fields.forEach((row, i) => {
                let j = 0;
                row.forEach((cell, j) => {
                    if (cell > 0) {
                        const player = cell === '1' ? p1 : p2;
                        $cell = $($($rows.get(i)).find('td').get(j));
                        $cell.html($('<i class="' + player + '">'));
                        $cell.addClass('occupied');
                    }

                    j++;
                });
                i++;
            });
        },
        init: function() {
            this.addFields();
        }
    }
};
