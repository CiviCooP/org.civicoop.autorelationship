<h3>{ts}Automatic relationships{/ts}</h3>

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
                <td>{$item.entity_label}</td>
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
