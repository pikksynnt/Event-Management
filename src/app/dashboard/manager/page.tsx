import React from "react";
import Link from "next/link";
import prisma from "@/lib/prisma";
import { requireEventManager } from "@/lib/auth";
import { StatCard } from "@/components/StatCard";
import { StatusBadge } from "@/components/StatusBadge";
import { EmptyState } from "@/components/EmptyState";
import {
  CalendarDays,
  Clock,
  CheckCircle2,
  XCircle,
  Users,
  ChevronRight,
  Filter,
} from "lucide-react";

interface PageProps {
  searchParams: Promise<{
    filter?: string;
  }>;
}

export default async function ManagerDashboardPage({ searchParams }: PageProps) {
  // Layer 2 Server-Side Role Authorization Check
  await requireEventManager();

  const { filter = "all" } = await searchParams;

  // Real database statistics queries
  const [totalEvents, submittedCount, approvedCount, rejectedCount] =
    await Promise.all([
      prisma.event.count(),
      prisma.event.count({ where: { status: "submitted" } }),
      prisma.event.count({ where: { status: "approved" } }),
      prisma.event.count({ where: { status: "rejected" } }),
    ]);

  // Construct query where clause based on selected filter
  const validFilters = ["submitted", "approved", "rejected"];
  const whereClause = validFilters.includes(filter)
    ? { status: filter }
    : {};

  // Fetch events list from database
  const events = await prisma.event.findMany({
    where: whereClause,
    orderBy: { createdAt: "desc" },
  });

  const filterTabs = [
    { id: "all", label: "Semua", count: totalEvents },
    { id: "submitted", label: "Menunggu Review", count: submittedCount },
    { id: "approved", label: "Disetujui", count: approvedCount },
    { id: "rejected", label: "Ditolak", count: rejectedCount },
  ];

  function formatDate(date: Date) {
    return new Intl.DateTimeFormat("id-ID", {
      day: "numeric",
      month: "short",
      year: "numeric",
    }).format(new Date(date));
  }

  return (
    <div className="space-y-8">
      {/* Page Header */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
            Dashboard Event Manager
          </h1>
          <p className="mt-1 text-sm text-slate-600">
            Tinjau, evaluasi, dan kelola seluruh pengajuan event dengan terorganisir.
          </p>
        </div>
      </div>

      {/* Database Statistics */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <StatCard
          title="Total Event"
          value={totalEvents}
          icon={CalendarDays}
          variant="default"
          description="Seluruh pengajuan event tercatat"
        />
        <StatCard
          title="Menunggu Review"
          value={submittedCount}
          icon={Clock}
          variant="amber"
          description="Perlu segera ditinjau manager"
        />
        <StatCard
          title="Event Disetujui"
          value={approvedCount}
          icon={CheckCircle2}
          variant="emerald"
          description="Telah disetujui & siap jalan"
        />
        <StatCard
          title="Event Ditolak"
          value={rejectedCount}
          icon={XCircle}
          variant="rose"
          description="Pengajuan ditolak dengan alasan"
        />
      </div>

      {/* Event List Section */}
      <div className="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        {/* Section Header & Filters */}
        <div className="p-6 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h2 className="text-lg font-bold text-slate-900">Daftar Event</h2>
            <p className="text-xs text-slate-500 mt-0.5">
              Menampilkan {events.length} event berdasarkan status yang dipilih.
            </p>
          </div>

          {/* Filter Pills */}
          <div className="flex items-center flex-wrap gap-1.5 p-1 bg-slate-100 rounded-lg border border-slate-200 text-xs">
            <span className="flex items-center gap-1 px-2.5 py-1 text-slate-500 font-medium">
              <Filter className="w-3.5 h-3.5" />
              Filter:
            </span>
            {filterTabs.map((tab) => {
              const isActive = filter === tab.id;
              return (
                <Link
                  key={tab.id}
                  href={`/dashboard/manager?filter=${tab.id}`}
                  className={`px-3 py-1.5 rounded-md font-medium transition-all ${
                    isActive
                      ? "bg-white text-slate-900 shadow-2xs font-semibold"
                      : "text-slate-600 hover:text-slate-900 hover:bg-slate-200/60"
                  }`}
                >
                  {tab.label}
                  <span
                    className={`ml-1.5 px-1.5 py-0.5 rounded-full text-[11px] ${
                      isActive
                        ? "bg-slate-900 text-white"
                        : "bg-slate-200 text-slate-700"
                    }`}
                  >
                    {tab.count}
                  </span>
                </Link>
              );
            })}
          </div>
        </div>

        {/* Content: Table or Empty State */}
        {events.length === 0 ? (
          <div className="p-6">
            <EmptyState
              title="Tidak ada event pada kategori ini"
              description="Tidak ada data event dengan status yang Anda pilih saat ini."
            />
          </div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
              <thead className="bg-slate-50/80 border-b border-slate-200 text-xs uppercase font-semibold text-slate-500">
                <tr>
                  <th scope="col" className="px-6 py-4">Judul Event</th>
                  <th scope="col" className="px-6 py-4">Jenis Event</th>
                  <th scope="col" className="px-6 py-4">Tanggal Pelaksanaan</th>
                  <th scope="col" className="px-6 py-4">Estimasi Tamu</th>
                  <th scope="col" className="px-6 py-4">Status</th>
                  <th scope="col" className="px-6 py-4 text-right">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-slate-100">
                {events.map((event) => (
                  <tr
                    key={event.id}
                    className="hover:bg-slate-50/60 transition-colors"
                  >
                    <td className="px-6 py-4">
                      <div className="font-semibold text-slate-900">
                        {event.title}
                      </div>
                      <p className="text-xs text-slate-500 line-clamp-1 max-w-sm mt-0.5">
                        {event.description}
                      </p>
                    </td>
                    <td className="px-6 py-4">
                      <span className="inline-flex px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        {event.eventType}
                      </span>
                    </td>
                    <td className="px-6 py-4 text-slate-600 whitespace-nowrap">
                      {formatDate(event.startDate)} - {formatDate(event.endDate)}
                    </td>
                    <td className="px-6 py-4 text-slate-600 whitespace-nowrap">
                      <div className="inline-flex items-center gap-1.5">
                        <Users className="w-4 h-4 text-slate-400" />
                        <span>{event.estimatedGuests.toLocaleString()} orang</span>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <StatusBadge status={event.status} />
                    </td>
                    <td className="px-6 py-4 text-right whitespace-nowrap">
                      <Link
                        href={`/dashboard/manager/events/${event.id}`}
                        className="inline-flex items-center gap-1 text-xs font-semibold text-slate-900 hover:text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition-colors"
                      >
                        Detail
                        <ChevronRight className="w-3.5 h-3.5" />
                      </Link>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
}
