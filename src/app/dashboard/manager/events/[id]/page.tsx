import React from "react";
import Link from "next/link";
import prisma from "@/lib/prisma";
import { requireEventManager } from "@/lib/auth";
import { StatusBadge } from "@/components/StatusBadge";
import { EventDetailActions } from "@/components/EventDetailActions";
import {
  ArrowLeft,
  Calendar,
  Users,
  Tag,
  Clock,
  AlertCircle,
  FileText,
} from "lucide-react";

interface PageProps {
  params: Promise<{
    id: string;
  }>;
}

export default async function EventDetailPage({ params }: PageProps) {
  // Layer 2 Server-Side Role Authorization Check
  await requireEventManager();

  const { id } = await params;
  const eventId = parseInt(id, 10);

  // Validate eventId number
  if (isNaN(eventId)) {
    return (
      <div className="flex flex-col items-center justify-center p-12 text-center bg-white rounded-xl border border-slate-200 shadow-sm max-w-lg mx-auto">
        <div className="w-12 h-12 mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
          <AlertCircle className="w-6 h-6" />
        </div>
        <h2 className="text-lg font-bold text-slate-900">Event tidak ditemukan</h2>
        <p className="mt-1 text-sm text-slate-500">
          ID event yang Anda cari tidak valid atau tidak tersedia di sistem.
        </p>
        <Link
          href="/dashboard/manager"
          className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-slate-900 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg transition-colors"
        >
          <ArrowLeft className="w-4 h-4" />
          Kembali ke Dashboard
        </Link>
      </div>
    );
  }

  // Fetch event details from database
  const event = await prisma.event.findUnique({
    where: { id: eventId },
  });

  if (!event) {
    return (
      <div className="flex flex-col items-center justify-center p-12 text-center bg-white rounded-xl border border-slate-200 shadow-sm max-w-lg mx-auto">
        <div className="w-12 h-12 mb-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
          <AlertCircle className="w-6 h-6" />
        </div>
        <h2 className="text-lg font-bold text-slate-900">Event tidak ditemukan</h2>
        <p className="mt-1 text-sm text-slate-500">
          Event dengan ID #{id} tidak ada dalam basis data.
        </p>
        <Link
          href="/dashboard/manager"
          className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-slate-900 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-lg transition-colors"
        >
          <ArrowLeft className="w-4 h-4" />
          Kembali ke Dashboard
        </Link>
      </div>
    );
  }

  function formatDateTime(date: Date) {
    return new Intl.DateTimeFormat("id-ID", {
      dateStyle: "full",
      timeStyle: "short",
    }).format(new Date(date));
  }

  return (
    <div className="space-y-6">
      {/* Back navigation link */}
      <div>
        <Link
          href="/dashboard/manager"
          className="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 px-3 py-1.5 rounded-lg transition-colors shadow-2xs"
        >
          <ArrowLeft className="w-3.5 h-3.5" />
          Kembali ke Dashboard
        </Link>
      </div>

      {/* Main Event Card */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        {/* Header */}
        <div className="p-6 sm:p-8 border-b border-slate-200">
          <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <div className="flex items-center gap-3">
                <span className="text-xs font-mono font-semibold text-slate-400">
                  EVENT #{event.id}
                </span>
                <StatusBadge status={event.status} />
              </div>
              <h1 className="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                {event.title}
              </h1>
            </div>

            {/* Actions for Manager */}
            <div className="shrink-0">
              <EventDetailActions
                eventId={event.id}
                currentStatus={event.status}
              />
            </div>
          </div>
        </div>

        {/* Rejection Notice Banner if rejected */}
        {event.status === "rejected" && event.rejectionReason && (
          <div className="bg-rose-50 border-b border-rose-200 p-6">
            <div className="flex items-start gap-3">
              <AlertCircle className="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
              <div>
                <h4 className="text-sm font-bold text-rose-900">
                  Alasan Penolakan Event:
                </h4>
                <p className="mt-1 text-sm text-rose-800 leading-relaxed">
                  {event.rejectionReason}
                </p>
              </div>
            </div>
          </div>
        )}

        {/* Event Key Meta Grid */}
        <div className="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-200 border-b border-slate-200 bg-slate-50/50">
          <div className="p-6 flex items-start gap-3.5">
            <div className="p-2.5 rounded-lg bg-slate-100 text-slate-700">
              <Tag className="w-5 h-5" />
            </div>
            <div>
              <p className="text-xs font-medium text-slate-500">Jenis Event</p>
              <p className="mt-0.5 text-base font-semibold text-slate-900">
                {event.eventType}
              </p>
            </div>
          </div>

          <div className="p-6 flex items-start gap-3.5">
            <div className="p-2.5 rounded-lg bg-slate-100 text-slate-700">
              <Users className="w-5 h-5" />
            </div>
            <div>
              <p className="text-xs font-medium text-slate-500">Estimasi Tamu</p>
              <p className="mt-0.5 text-base font-semibold text-slate-900">
                {event.estimatedGuests.toLocaleString()} orang
              </p>
            </div>
          </div>

          <div className="p-6 flex items-start gap-3.5">
            <div className="p-2.5 rounded-lg bg-slate-100 text-slate-700">
              <Calendar className="w-5 h-5" />
            </div>
            <div>
              <p className="text-xs font-medium text-slate-500">Dibuat pada</p>
              <p className="mt-0.5 text-base font-semibold text-slate-900">
                {formatDateTime(event.createdAt)}
              </p>
            </div>
          </div>
        </div>

        {/* Schedule & Description */}
        <div className="p-6 sm:p-8 space-y-8">
          {/* Schedule */}
          <div>
            <h3 className="text-sm font-semibold uppercase tracking-wider text-slate-500 flex items-center gap-2 mb-3">
              <Clock className="w-4 h-4" />
              Jadwal Pelaksanaan
            </h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div className="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                <span className="text-xs font-medium text-slate-500 block">
                  Tanggal & Waktu Mulai
                </span>
                <span className="text-sm font-semibold text-slate-900 mt-1 block">
                  {formatDateTime(event.startDate)}
                </span>
              </div>
              <div className="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                <span className="text-xs font-medium text-slate-500 block">
                  Tanggal & Waktu Selesai
                </span>
                <span className="text-sm font-semibold text-slate-900 mt-1 block">
                  {formatDateTime(event.endDate)}
                </span>
              </div>
            </div>
          </div>

          {/* Description */}
          <div>
            <h3 className="text-sm font-semibold uppercase tracking-wider text-slate-500 flex items-center gap-2 mb-3">
              <FileText className="w-4 h-4" />
              Deskripsi Event
            </h3>
            <div className="p-5 rounded-xl border border-slate-200 bg-white">
              <p className="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                {event.description}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
