<?php

/**
 * Converts HTMLPurifier_ConfigSchema_Interchange to our runtime
 * representation used to perform checks on user configuration.
 */
class HTMLPurifier_ConfigSchema_Builder_ConfigSchema
{
    
    /**
     * Builds a new HTMLPurifier_ConfigSchema instance from the provided configuration interchange data..
     *
     * This method takes an HTMLPurifier_ConfigSchema_Interchange object as input and uses its data to construct a new HTMLPurifier_ConfigSchema instance.
     * It adds all the namespaces, directives, allowed values, aliases, and value aliases defined in the interchange to the new schema object, and then performs post-processing to finalize the schema.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange The configuration interchange data to use for building the new schema.
     * @return HTMLPurifier_ConfigSchema The newly constructed HTMLPurifier_ConfigSchema instance.
     */
    /**
     * Builds a new HTMLPurifier_ConfigSchema instance from the provided configuration interchange data..
     *
     * This method takes an HTMLPurifier_ConfigSchema_Interchange object as input and uses its data to construct a new HTMLPurifier_ConfigSchema instance.
     * It adds all the namespaces, directives, allowed values, aliases, and value aliases defined in the interchange to the new schema object, and then performs post-processing to finalize the schema.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange The configuration interchange data to use for building the new schema.
     * @return HTMLPurifier_ConfigSchema The newly constructed HTMLPurifier_ConfigSchema instance.
     * @note This method modifies global state.
     */
    /**
     * Builds a new HTMLPurifier_ConfigSchema instance from the provided configuration interchange data..
     *
     * This method takes an HTMLPurifier_ConfigSchema_Interchange object as input and uses its data to construct a new HTMLPurifier_ConfigSchema instance.
     * It adds all the namespaces, directives, allowed values, aliases, and value aliases defined in the interchange to the new schema object, and then performs post-processing to finalize the schema.
     *
     * @param HTMLPurifier_ConfigSchema_Interchange $interchange The configuration interchange data to use for building the new schema.
     * @return HTMLPurifier_ConfigSchema The newly constructed HTMLPurifier_ConfigSchema instance.
     * @note This method modifies global state.
     */
    public function build($interchange) {
        $schema = new HTMLPurifier_ConfigSchema();
        foreach ($interchange->namespaces as $n) {
            $schema->addNamespace($n->namespace);
        }
        foreach ($interchange->directives as $d) {
            $schema->add(
                $d->id->namespace,
                $d->id->directive,
                $d->default,
                $d->type,
                $d->typeAllowsNull
            );
            if ($d->allowed !== null) {
                $schema->addAllowedValues(
                    $d->id->namespace,
                    $d->id->directive,
                    $d->allowed
                );
            }
            foreach ($d->aliases as $alias) {
                $schema->addAlias(
                    $alias->namespace,
                    $alias->directive,
                    $d->id->namespace,
                    $d->id->directive
                );
            }
            if ($d->valueAliases !== null) {
                $schema->addValueAliases(
                    $d->id->namespace,
                    $d->id->directive,
                    $d->valueAliases
                );
            }
        }
        $schema->postProcess();
        return $schema;
    }
    
}
