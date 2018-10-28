/* $Id: nc_forum2_admin.class.js 5125 2011-08-04 08:00:08Z denis $ */

/**
 * Constructor function
 */
nc_Forum2 = function (options) {
  
    this.MODULE_PATH = options.MODULE_PATH || NETCAT_PATH + 'modules/forum2/';
    this.ADMIN_TEMPLATE = options.ADMIN_TEMPLATE || ADMIN_PATH + 'skins/default/';
  
}

nc_Forum2.prototype = {
    /**
   * Element "changed" status array
   */
    changeArr: new Array,
    /**
   * Element "loaded" status array
   */
    loadedArr: new Array,
  
    /**
   * Check drop after drag possibility
   * for groups
   */
    groupDropCheck: function () {
    
        var dragged = top.dragManager.draggedInstance,
        target  = top.dragManager.droppedInstance;
    
        if ( !(dragged.type=='treeGroup' || dragged.type=='treeGroupID') ) return false;
    
        return true;
    },

    /**
   * Drop after drag
   * for groups
   *
   * nc_forum2Obj object getted from global context
   */
    groupDropComplete: function () {
        var dragged = top.dragManager.draggedInstance,
        target  = top.dragManager.droppedInstance;
    
        // ajax
        var xhr = new httpRequest;
        // request
        xhr.request('GET', nc_forum2Obj.MODULE_PATH + 'ajax/update_tree.php', {
            type:'group',
            from_id: dragged.id,
            to_id:target.id
            });
    
        // get request JSON
        var response = xhr.getResponseText();
    
        if (response!=0) nc_forum2Obj.loadForums(response);
    },

    /**
   * Check drop after drag possibility
   * for forums
   */
    forumDropCheck: function () {
        var dragged = top.dragManager.draggedInstance,
        target  = top.dragManager.droppedInstance;

        if (dragged.typeNum==target.id) return false;
    
        if (dragged.type=='treeGroup' || dragged.type=='treeGroupID') return false;
    
        return true;
    },

    /**
   * Drop after drag
   * for forums
   *
   * nc_forum2Obj object getted from global context
   */
    forumDropComplete: function () {
        var dragged = top.dragManager.draggedInstance,
        target  = top.dragManager.droppedInstance;
    
        // ajax
        var xhr = new httpRequest;
        // request
        xhr.request('GET', nc_forum2Obj.MODULE_PATH + 'ajax/update_tree.php', {
            type:'forum',
            from_id: dragged.typeNum,
            to_id:target.id,
            obj_id:dragged.id
            });
    
        // get request JSON
        var response = xhr.getResponseText();
    
        if (response!=0) nc_forum2Obj.loadForums(response);
    },

    /**
   * Load forums data into the parent container
   *
   * @param int parent id
   */
    loadForums: function (parentId) {
        var parent = document.getElementById('parent' + parentId);
        var plus = document.getElementById('plus' + parentId);
    
        // ajax
        var xhr = new httpRequest;
        // request
        xhr.request('GET', this.MODULE_PATH + 'ajax/get_forums.php', {
            parent_id: parentId
        });
        // get request JSON
        var responseJson = xhr.getResponseText();
    
        if (!responseJson) return false;
    
        // eval request
        res = jQuery.parseJSON(responseJson);// eval('(' + responseJson.replace(/\n/g, "%NL2BR").replace(/\r/g, "") + ')');
        // insert HTML
        parent.innerHTML = res.html.replace(/%NL2BR/g, "\n");

        for (i = 0; i < res.forums.length; i++) {
            top.dragManager.addDraggable( document.getElementById(res.forums[i]) );
        }
    
        for (i = 0; i < res.groups.length; i++) {
            // draggable
            top.dragManager.addDraggable( document.getElementById(res.groups[i]) );
            // droppable
            top.dragManager.addDroppable( document.getElementById(res.groups[i]), this.forumDropCheck, this.forumDropComplete, {
                name: 'arrowRight',
                top: 13,
                left: 0
            } );
            top.dragManager.addDroppable( document.getElementById(res.groups[i].replace(/treeGroup/g, "treeGroupID")), this.groupDropCheck, this.groupDropComplete, {
                name: 'line',
                top: 13,
                left: 0
            } );
        }
    
        // plus button
        plus.setAttribute("src", this.ADMIN_TEMPLATE + "img/i_minus.png");
        plus.onclick = (function (prnt, pls, tpl) {
            return function () {
                if (prnt.style.display=="none") {
                    prnt.style.display = "block";
                    pls.setAttribute("src", tpl + "img/i_minus.png");
                }
                else {
                    prnt.style.display = "none";
                    pls.setAttribute("src", tpl + "img/i_plus.png");
                }
            }
        })(parent, plus, this.ADMIN_TEMPLATE);

    },

    /**
   * Load groups data
   *
   * @param int group id
   */
    loadGroup: function (groupId) {
        var list = document.getElementById('Group_ID');
        var name = document.getElementById('Group_Name');
        var desc = document.getElementById('Group_Description');
        var priority = document.getElementById('Group_Priority');
        var grpfblk = document.getElementById('GroupForumBlock');
        var grpdelblk = document.getElementById('GroupDeleteBlock');
    
        if (groupId==0) {
            name.value = "";
            desc.value = "";
            priority.value = "0";
            grpfblk.style.display = "block";
            grpdelblk.style.display = "none";
            return;
        }
    
        grpfblk.style.display = "none";
        grpdelblk.style.display = "block";
        name.disabled = true;
        desc.disabled = true;
        priority.disabled = true;

        // ajax
        var xhr = new httpRequest;
        // request
        xhr.request('GET', this.MODULE_PATH + 'ajax/get_group.php', {
            group_id: groupId
        });
        // synchronous request
        var responseJson = xhr.getResponseText();
        // return if no result
        if (!responseJson) return;
        
        result = eval('(' + responseJson + ')');
    
        name.value = result.name;
        desc.value = result.description;
        priority.value = result.priority;
    
        name.disabled = false;
        desc.disabled = false;
        priority.disabled = false;
    },
  
    /**
   * Load element information
   *
   * @param int node id
   */
    loadInfo: function (nodeId) {
        var node = document.getElementById('info' + nodeId);
        var infoDescription = document.getElementById('infoDescription' + nodeId);
    
        if (!this.loadedArr[nodeId]) {
            // ajax
            var xhr = new httpRequest;
            // request
            xhr.request('GET', this.MODULE_PATH + 'ajax/get_info.php', {
                node_id: nodeId
            });
            // synchronous request
            infoDescription.value = xhr.getResponseText();
            // set "loaded" flag
            this.loadedArr[nodeId] = true;
        }
    
        node.style.display = "block";
        infoDescription.focus();
    },
  
    /**
   * Save element information
   *
   * @param int node id
   */
    saveInfo: function (nodeId) {
        var node = document.getElementById('info' + nodeId);
        var infoDescription = document.getElementById('infoDescription' + nodeId);
    
        if ( this.getChangeInfo(nodeId) ) {
            // ajax
            var xhr = new httpRequest;
            // disable element
            infoDescription.disabled = true;
            // request
            xhr.request('GET', this.MODULE_PATH + 'ajax/save_info.php', {
                node_id: nodeId,
                description: infoDescription.value
                });
            // synchronous request
            var response = xhr.getResponseText();
        }
    
        // hide node
        node.style.display = "none";
        // enable element
        if (infoDescription.disabled) infoDescription.disabled = false;
    
        // set change status 0
        this.setChangeInfo(nodeId, 0);
    },
  
    /**
   * Check element information chage status
   *
   * @param int node id
   *
   * @return bool result
   */
    getChangeInfo: function (nodeId) {
        // return status
        return this.changeArr[nodeId];
    },
  
    /**
   * Set element information chage status
   *
   * @param int node id
   * @param bool flag
   */
    setChangeInfo: function (nodeId, flag) {
        // set status
        this.changeArr[nodeId] = flag;
    }

}