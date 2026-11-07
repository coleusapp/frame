import { Collection, Request, Resource } from '@coleus/support/types/resource';
import type { InjectionKey, Ref } from 'vue';

export type OptionCollection = { data: { label: string; value: string | number | null }[] };

export type FrameData = {
    id: number;
    name: string;
    search_query: string | null;
    sources: string[];
    interval_seconds: number;
    is_active: boolean;
    artworks_count: number;
};

export type FrameResource = Resource<FrameData>;
export type FrameRequest = Request<Omit<FrameData, 'artworks_count'>>;
export type FrameCollection = Collection<FrameData>;

export function updateRequest({ name, search_query, sources, interval_seconds, is_active }: FrameData): FrameRequest {
    return { name, search_query, sources, interval_seconds, is_active };
}

export const resourceKey = Symbol() as InjectionKey<FrameResource>;
export const sourcesKey = Symbol() as InjectionKey<OptionCollection>;
// A ref, because regenerating the token reloads this same page with a new URL.
export const kioskUrlKey = Symbol() as InjectionKey<Ref<string | null>>;
