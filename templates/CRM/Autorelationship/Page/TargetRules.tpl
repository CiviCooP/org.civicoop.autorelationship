<h4>{ts}Automatic relationships{/ts}</h4>

{if (count($interfaces) > 0)}
        
        <select name="addTargetRule" onchange="addTargetRule(this);">
            <option>-- {ts}New automatic relationship rule{/ts} --</option>
            {foreach from=$interfaces item=interface}
                <option value="{$interface->getEntitySystemName()}">{$interface->getEntityHumanName()}</option>
            {/foreach}
        </select>
{/if}

{if (count($entities) > 0)}

    <table class="targets">
        <tr>
            <th>{ts}Based on{/ts}</th>
            <th>{ts}Value{/ts}</th>
            <th></th>
        </tr>
        {foreach from=$entities item=entity}
            {foreach from=$entity item=item}
            <tr>
                <td>{$item.relationship_description}</td>
                <td>{$item.label}</td>
                <td>
                    {assign var='delete_q' value='action=delete&entity='|cat:$item.entity|cat:'&entity_id='|cat:$item.entity_id|cat:'&cid='|cat:$contactId}
                    <a href="{crmURL p='civicrm/contact/tab/autorelationship_targetrules' q=$delete_q h=0}">{ts}Delete{/ts}</a>
                </td>
            </tr>
            {/foreach}
        {/foreach}
    </table>
{/if}

{literal}
<script type="text/javascript">

function addTargetRule(select) {
    var entity = select.options[select.selectedIndex].value;
    if (typeof entity != 'undefined' && entity) {
        window.location = CRM.url('civicrm/contact/tab/autorelationship_targetrules', {
            'cid': '{/literal}{$contactId}{literal}',
            'entity': entity,
            'action': 'add'
        });
    }
    
}
</script>
{/literal}